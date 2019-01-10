<?php

namespace Krixon\SamlClient\Document;

final class XmlNamespace
{
    private const SAML  = 'xmlns:saml';
    private const SAMLP = 'xmlns:samlp';
    private const DS    = 'xmlns:ds';
    private const XS    = 'xmlns:xs';
    private const XSI   = 'xmlns:xsi';
    private const XENC  = 'xmlns:xenc';

    private const MAP = [
        self::SAML  => 'urn:oasis:names:tc:SAML:2.0:assertion',
        self::SAMLP => 'urn:oasis:names:tc:SAML:2.0:protocol',
        self::DS    => 'http://www.w3.org/2000/09/xmldsig#',
        self::XS    => 'http://www.w3.org/2001/XMLSchema',
        self::XSI   => 'http://www.w3.org/2001/XMLSchema-instance',
        self::XENC  => 'http://www.w3.org/2001/04/xmlenc#',
    ];

    private $qualifiedName;
    private $prefix;


    private function __construct(string $ns)
    {
        $this->qualifiedName = $ns;
        $this->prefix        = substr($ns, 6);
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


    public static function xenc() : self
    {
        return new self(self::XENC);
    }


    public static function ds() : self
    {
        return new self(self::DS);
    }


    public function qualifiedName() : string
    {
        return $this->qualifiedName;
    }


    public function prefix() : string
    {
        return $this->prefix;
    }


    public function urn() : string
    {
        return self::MAP[$this->qualifiedName];
    }


    public function equals(XmlNamespace $other) : bool
    {
        return $this->qualifiedName === $other->qualifiedName;
    }


    public function registerWithXPath(\DOMXPath $xpath) : void
    {
        $xpath->registerNamespace($this->prefix, $this->urn());
    }
}
