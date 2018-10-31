<?php

namespace Krixon\SamlClient\Config\Security;

final class SigningAlgorithm
{
    private const RSA_SHA256 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
    private const RSA_SHA384 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384';
    private const RSA_SHA512 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512';
    
    private $algorithm;


    private function __construct(string $algorithm)
    {
        $this->algorithm = $algorithm;
    }
}
