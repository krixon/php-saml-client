<?php

namespace Krixon\SamlClient;

use Krixon\SamlClient\Protocol\Binding;

class AssertionConsumerService
{
    private $url;
    private $binding;


    public function __construct(string $url, Binding $binding)
    {
        $this->url     = $url;
        $this->binding = $binding;
    }
}
