<?php

namespace Krixon\SamlClient\Document;

use Krixon\SamlClient\Document\Exception\DecryptionFailed;
use Krixon\SamlClient\Security\Key;
use RobRichards\XMLSecLibs\XMLSecEnc;

class Document extends \DOMDocument
{
    public static function import(string $xml)
    {
        // Prevent XXE attacks caused by referencing external entities in the XML.
        // https://www.owasp.org/index.php/XML_External_Entity_(XXE)_Processing
        if (stripos($xml, '<!ENTITY') !== false) {
            throw new Exception\InvalidDocument('Use of <!ENTITY> in XML is not supported.');
        }

        // DomDocument::loadXML will trigger a warning on invalid XML rather than throwing.
        // To work around that, register a temporary error handler.
        set_error_handler(function ($number, $message) {
            if ($number === E_WARNING && stripos($message, 'DOMDocument::loadXML') !== false) {
                throw new Exception\InvalidDocument($message);
            }
            // Invoke the previous error handler.
            return false;
        });

        // libxml2 version >=2.6 contains entity substitution limits designed to prevent both exponential and
        // quadratic attacks. We should also have caught any uses of ENTITY above. To be safe and to support earlier
        // libxml2 versions however, explicitly disable entity loading.
        $entityLoader = libxml_disable_entity_loader(true);

        $document = new static();

        try {
            $result = $document->loadXML($xml);
        } finally {
            libxml_disable_entity_loader($entityLoader);
            restore_error_handler();
        }

        if (!$result) {
            // Errors should already have been caught by the error handler, but just in case...
            throw new Exception\InvalidDocument('Unable to load XML from string.');
        }

        if (null === $document->documentElement) {
            throw new Exception\InvalidDocument('Unable to load XML from string. A root element is required.');
        }

        return $document;
    }


    public function toString() : string
    {
        $this->formatOutput       = false;
        $this->preserveWhiteSpace = false;

        // Render the document element itself (ie the root node) as we don't want the XML declaration etc.
        return $this->saveXML($this->documentElement);
    }


    public function root() : \DOMElement
    {
        return $this->documentElement;
    }


    public function registerNamespace(XmlNamespace $namespace) : void
    {
        $this->root()->setAttributeNS('http://www.w3.org/2000/xmlns/', $namespace->qualifiedName(), $namespace->urn());
    }


    public function decrypt(Key $privateKey) : self
    {
        $document = clone $this;

        // Locate the encrypted data in the document.

        $enc           = new XMLSecEnc();
        $encryptedData = $enc->locateEncryptedData($document);

        if (!$encryptedData instanceof \DOMElement) {
            throw new DecryptionFailed('Decryption failed. Cannot locate encrypted data in response.');
        }

        // Locate the key data in the document.

        $enc->setNode($encryptedData);
        $enc->type = $encryptedData->getAttribute('Type');
        $keyData   = $enc->locateKey();

        if (!$keyData) {
            throw new DecryptionFailed('Decryption failed. Cannot locate key data in response.');
        }

        $key         = null;
        $keyInfoData = $enc->locateKeyInfo($keyData);

        try {
            if ($keyInfoData) {
                if ($keyInfoData->isEncrypted) {
                    $keyInfoData->loadKey($privateKey->toString());
                    $key = $keyInfoData->encryptedCtx->decryptKey($keyInfoData);
                } else {
                    // Symmetric encryption key.
                    $keyInfoData->loadKey($privateKey->toString());
                }
            }

            if (empty($keyData->key)) {
                $keyData->loadKey($key);
            }
        } catch (\Exception $e) {
            throw new DecryptionFailed('Decryption failed. Cannot load decryption key.', $e);
        }

        try {
            $decryptedXml = $enc->decryptNode($keyData, false);
        } catch (\Exception $e) {
            throw new DecryptionFailed('Decryption failed.', $e);
        }

        $decrypted = Document::import($decryptedXml);

        // At this stage we have decrypted XML representing the first encrypted element in the document.
        // Replace that element with the decrypted version.

        if ($encryptedData->parentNode instanceof \DOMDocument) {
            // Encrypted data was previously at the root; nothing else to do.
            return $decrypted;
        }

        // Replace the encrypted element with the decrypted element.

        $newNode        = $document->importNode($decrypted->root(), true);
        $encryptionRoot = $encryptedData->parentNode;

        $encryptionRoot->parentNode->replaceChild($newNode, $encryptionRoot);

        return $document;
    }
}