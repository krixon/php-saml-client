<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Protocol\Binding;
use Psr\Http\Message\UriInterface;

/**
 * An instruction on how to carry out a login request to an IDP.
 */
class RequestInstruction
{
    private $uri;
    private $payload;
    private $binding;


    public function __construct(UriInterface $uri, string $payload, Binding $binding)
    {
        $this->uri     = $uri;
        $this->payload = $payload;
        $this->binding = $binding;
    }


    public function binding() : Binding
    {
        return $this->binding;
    }


    public function relayState() : ?string
    {
        return $this->relayState;
    }
}