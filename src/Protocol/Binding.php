<?php

namespace Krixon\SamlClient\Protocol;

final class Binding
{
    private const HTTP_POST     = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';
    private const HTTP_REDIRECT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

    private $binding;


    private function __construct(string $binding)
    {
        $this->binding = $binding;
    }
    
    
    public static function httpPost() : self
    {
        return new self(self::HTTP_POST);
    }


    public static function httpRedirect() : self
    {
        return new self(self::HTTP_REDIRECT);
    }


    public function toString() : string
    {
        return $this->binding;
    }


    public function isHttpPost() : bool
    {
        return $this->binding === self::HTTP_POST;
    }


    public function isHttpRedirect() : bool
    {
        return $this->binding === self::HTTP_REDIRECT;
    }
}
