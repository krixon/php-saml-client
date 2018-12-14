<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Document\Element;
use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Security\Algorithm;
use Krixon\SamlClient\Security\Key;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class Signature extends Element
{
    private $algorithm;
    private $privateKey;
    private $publicKey;
    private $useRfc3986;


    public function __construct(Algorithm $algorithm, Key $privateKey, Key $publicKey = null, $useRfc3986 = true)
    {
        $this->privateKey = new XMLSecurityKey($this->algorithm->toString(), ['type' =>'private']);
        $this->privateKey->passphrase = $privateKey->passphrase() ?: '';
        $this->privateKey->loadKey($privateKey->toString(), false);

        $this->algorithm  = $algorithm;
        $this->publicKey  = $publicKey;
        $this->useRfc3986 = $useRfc3986;
    }


    public function appendTo(SamlDocument $document, \DOMNode $parent) : void
    {
        $sig = new XMLSecurityDSig();

        $sig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

        $sig->addReference(
            $document,
            XMLSecurityDSig::SHA256,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature']
        );

        $sig->sign($this->privateKey);

        if (null !== $this->publicKey) {
            $sig->add509Cert($this->publicKey->toString());
        }

        $sig->appendSignature($parent, true);
    }


    public function sign(string $data) : string
    {
        return $this->privateKey->signData($data);
    }


    public function useRfc3986() : bool
    {
        return $this->useRfc3986;
    }


    public function algorithm() : Algorithm
    {
        return $this->algorithm;
    }
}