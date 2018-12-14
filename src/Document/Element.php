<?php

namespace Krixon\SamlClient\Document;

abstract class Element implements WritableElement
{
    final protected static function appendDomElement(\DOMNode $parent, string $name, string $value = null) : \DOMElement
    {
        $document = $parent instanceof \DOMDocument ? $parent : $parent->ownerDocument;
        $element  = $document->createElement($name, $value);

        $parent->appendChild($element);

        return $element;
    }
}
