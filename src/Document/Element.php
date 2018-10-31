<?php

namespace Krixon\SamlClient\Document;

abstract class Element
{
    public abstract function appendTo(SamlDocument $document, \DOMNode $parent) : void;


    final protected static function appendDomElement(\DOMNode $parent, string $name) : \DOMElement
    {
        $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
        $element  = $document->createElement($name);

        $parent->appendChild($element);

        return $element;
    }
}
