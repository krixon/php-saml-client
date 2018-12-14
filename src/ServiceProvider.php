<?php

namespace Krixon\SamlClient;

use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Protocol\AssertionConsumerService;
use Krixon\SamlClient\Protocol\Issuer;

class ServiceProvider
{
    private $id;
    private $name;
    private $assertionConsumerService;


    public function __construct(string $id, Name $name = null, AssertionConsumerService $acs = null)
    {
        $this->id                       = $id;
        $this->name                     = $name;
        $this->assertionConsumerService = $acs;
    }


    public function applyToLoginRequest(SamlDocument $document, \DOMElement $root) : void
    {
        if (null !== $this->name) {
            $root->setAttribute('ProviderName', $this->name->toString());
        }

        if (null !== $this->assertionConsumerService) {
            $this->assertionConsumerService->setAttributesOn($root);
        }

        $issuer = new Issuer($this->id);

        $issuer->appendTo($document, $root);
    }
}
