<?php

namespace Krixon\SamlClient\Config\Security;

final class DigestAlgorithm
{
    private const SHA256 = 'http://www.w3.org/2001/04/xmlenc#sha256';
    private const SHA384 = 'http://www.w3.org/2001/04/xmldsig-more#sha384';
    private const SHA512 = 'http://www.w3.org/2001/04/xmlenc#sha512';
    
    private $algorithm;


    private function __construct(string $algorithm)
    {
        $this->algorithm = $algorithm;
    }
}
