<?php

namespace Krixon\SamlClient\Document;

final class XmlNamespace
{
    private const SAML  = 'xmlns:saml';
    private const SAMLP = 'xmlns:samlp';

    private const MAP = [
        self::SAML  => 'urn:oasis:names:tc:SAML:2.0:assertion',
        self::SAMLP => 'urn:oasis:names:tc:SAML:2.0:protocol',
    ];

    private $qualifiedName;


    private function __construct(string $ns)
    {
        $this->qualifiedName = $ns;
    }


    /**
     * @return self[]
     */
    public static function all() : array
    {
        $instances = [];

        foreach (array_keys(self::MAP) as $qualifiedName) {
            $instances[] = new self($qualifiedName);
        }

        return $instances;
    }


    public static function saml() : self
    {
        return new self(self::SAML);
    }


    public static function samlp() : self
    {
        return new self(self::SAMLP);
    }


    public function qualifiedName() : string
    {
        return $this->qualifiedName;
    }


    public function urn() : string
    {
        return self::MAP[$this->qualifiedName];
    }


    public function equals(XmlNamespace $other) : bool
    {
        return $this->qualifiedName === $other->qualifiedName;
    }
}
