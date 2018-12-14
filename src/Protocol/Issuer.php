<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Document\Element;
use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Document\XmlNamespace;

final class Issuer extends Element
{
    private $id;


    public function __construct(string $id)
    {
        $this->id = $id;
    }


    public function appendTo(SamlDocument $document, \DOMNode $parent) : void
    {
        $document->registerNamespace(XmlNamespace::saml());

        self::appendDomElement($parent, 'saml:Issuer', $this->id);
    }
}