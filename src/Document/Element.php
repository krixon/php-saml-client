<?php

namespace Krixon\SamlClient\Document;

abstract class Element implements WritableElement
{
    final protected static function appendDomElement(\DOMNode $parent, string $name) : \DOMElement
    {
        $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
        $element  = $document->createElement($name);

        $parent->appendChild($element);

        return $element;
    }
}
