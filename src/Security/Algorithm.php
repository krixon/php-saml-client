<?php

namespace Krixon\SamlClient\Security;

use RobRichards\XMLSecLibs\XMLSecurityKey;

final class Algorithm
{
    private $algorithm;


    private function __construct(string $algorithm)
    {
        $this->algorithm = $algorithm;
    }


    public static function dsaSha1() : self
    {
        // Must be supported for the DEFLATE encoding of messages which use the HTTP-Redirect binding.
        return new self(XMLSecurityKey::DSA_SHA1);
    }


    public static function rsaSha1() : self
    {
        // Must be supported for the DEFLATE encoding of messages which use the HTTP-Redirect binding.
        return new self(XMLSecurityKey::RSA_SHA1);
    }


    public static function rsaSha256() : self
    {
        return new self(XMLSecurityKey::RSA_SHA256);
    }


    public static function rsaSha384() : self
    {
        return new self(XMLSecurityKey::RSA_SHA384);
    }


    public static function rsaSha512() : self
    {
        return new self(XMLSecurityKey::RSA_SHA512);
    }


    public function toString() : string
    {
        return $this->algorithm;
    }
}