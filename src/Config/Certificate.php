<?php

namespace Krixon\SamlClient\Config;

final class Certificate
{
    private $certificate;
    private $key;


    public function __construct(string $certificate, string $key)
    {
        $this->certificate = $certificate;
        $this->key         = $key;
    }
}
