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
        // TODO: This method and decryptElement can be extracted to an external service.
        $document = $this;
        $enc      = new XMLSecEnc();

        while ($encryptedData = $enc->locateEncryptedData($document)) {
            if (!$encryptedData instanceof \DOMElement) {
                throw new DecryptionFailed('Decryption failed. Cannot locate encrypted data in response.');
            }

            $document = self::decryptElement($enc, $encryptedData, $privateKey);
        }

        return $document;
    }


    private static function decryptElement(XMLSecEnc $enc, \DOMElement $element, Key $privateKey) : SamlDocument
    {
        $enc->setNode($element);

        $enc->type = $element->getAttribute('Type');
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

        // This is a workaround for the case where only a subset of the XML document was encrypted.
        // In that case, we may miss the namespaces needed to parse the XML as they might be defined on a parent
        // element which is not present in $decryptedXml.

        $decryptedXml = sprintf(
            '<root %s>%s</root>',
            XmlNamespace::attributeString(),
            $decryptedXml
        );

        $decrypted = Document::import($decryptedXml);

        // At this stage we have decrypted XML representing the first encrypted element in the document.
        // Replace that element with the decrypted version.

        /** @var SamlDocument $document */
        $document      = $element->ownerDocument;
        $newNode       = $document->importNode($decrypted->documentElement->firstChild, true);
        $nodeToReplace = $element;

        // Some nodes contain encrypted data rather than being directly encrypted.
        // This means we sometimes need to replace the parent of the EncryptedData rather than the EncryptedData itself.
        // TODO: Is there a nicer way to handle this than checking for specific nodes?
        $encryptionContainers = [
            'EncryptedAssertion',
            'EncryptedID'
        ];

        if (in_array($nodeToReplace->parentNode->localName, $encryptionContainers, true)) {
            $nodeToReplace = $element->parentNode;
        }

        $nodeToReplace->parentNode->replaceChild($newNode, $nodeToReplace);

        return $document;
    }
}