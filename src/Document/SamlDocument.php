<?php

namespace Krixon\SamlClient\Document;

final class SamlDocument extends \DOMDocument
{
    /** @noinspection PhpHierarchyChecksInspection LSP doesn't apply to constructors! */
    private function __construct()
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
            if ($number === E_WARNING && stripos($message, 'DOMDocument::loadXML')) {
                throw new Exception\InvalidDocument($message);
            }
            // Invoke the previous error handler.
            return false;
        });

        // libxml2 version >=2.6 contains entity substitution limits designed to prevent both exponential and
        // quadratic attacks. We should also have caught any uses of ENTITY above. To be safe and to support earlier
        // libxml2 versions however, explicitly disable entity loading.
        $entityLoader = libxml_disable_entity_loader(true);

        $document = new self();

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

        $version = $document->documentElement->getAttribute('Version');
        if ($version !== '2.0') {
            throw Exception\InvalidDocument::unsupportedSamlVersion($version);
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


    public function query(string $expression, \DOMElement $context = null) : \DOMNodeList
    {
        $xpath   = new \DOMXPath($this);
        $context = $context ?: $this->documentElement;

        // TODO: Might not be needed - see 3rd arg to XPath::query.
//        foreach (XmlNamespace::all() as $namespace) {
//            $xpath->registerNamespace($namespace->qualifiedName(), $namespace->urn());
//        }

        return $xpath->query($expression, $context);
    }


    /**
     * Removes the <ds:Signature> element on the document itself. Signatures on other elements such as a signed
     * assertion are not removed.
     *
     * This is necessary if the
     */
    public function removeSignature() : void
    {

    }
}
