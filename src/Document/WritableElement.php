<?php

namespace Krixon\SamlClient\Document;

interface WritableElement
{
    public function appendTo(SamlDocument $document, \DOMNode $parent) : void;
}
