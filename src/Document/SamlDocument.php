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
            $document->useNamespace($namespace);
        }

        return $document;
    }


    public function root() : \DOMElement
    {
        return $this->documentElement;
    }


    public function useNamespace(XmlNamespace $namespace) : void
    {
        $this->root()->setAttributeNS('http://www.w3.org/2000/xmlns/', $namespace->qualifiedName(), $namespace->urn());
    }
}
