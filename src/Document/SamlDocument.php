<?php

namespace Krixon\SamlClient\Document;

final class SamlDocument extends Document
{
    /** @noinspection PhpHierarchyChecksInspection LSP doesn't apply to constructors! */
    protected function __construct()
    {
        parent::__construct();
    }


    public static function create(string $rootNode, XmlNamespace ...$namespaces) : self
    {
        $document = new self();
        $root     = $document->createElement($rootNode);

        $document->appendChild($root);

        // Note: namespaces must be configured after adding the root node to the document.
        // The protocol namespace is always set.
        $namespaces[] = XmlNamespace::samlp();

        foreach ($namespaces as $namespace) {
            // useNamespace() is idempotent, so don't bother de-duping the array.
            $document->registerNamespace($namespace);
        }

        return $document;
    }


    public static function import(string $xml) : self
    {
        $document = parent::import($xml);

        $version = $document->documentElement->getAttribute('Version');
        if ($version !== '2.0') {
            throw Exception\InvalidDocument::unsupportedSamlVersion($version);
        }

        return $document;
    }


    public function query(string $expression, \DOMElement $context = null) : \DOMNodeList
    {
        $xpath   = new \DOMXPath($this);
        $context = $context ?: $this->documentElement;

        // TODO: Might not be needed - see 3rd arg to XPath::query.
        foreach (XmlNamespace::all() as $namespace) {
            $xpath->registerNamespace($namespace->prefix(), $namespace->urn());
        }

//        $xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
//        $xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
//        $xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
//        $xpath->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');

        return $xpath->query($expression, $context);
    }


    public function hasAssertion() : bool
    {
        return $this->getElementsByTagName('Assertion')->length > 0;
    }


    public function hasEncryptedAssertion() : bool
    {
        return $this->getElementsByTagName('EncryptedAssertion')->length > 0;
    }
}
