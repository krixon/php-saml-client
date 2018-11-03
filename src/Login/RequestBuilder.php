<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Config\Organisation;
use Krixon\SamlClient\Protocol\Binding;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\Protocol\RequestId;
use Psr\Http\Message\UriInterface;

class RequestBuilder
{
    private $id;
    private $uri;
    private $issueInstant;
    private $parameters;
    private $forceAuthn;
    private $passive;
    private $requestedAuthnContext;
    private $nameIdPolicy;
    private $providerName;
    private $relayState;
    private $binding;


    public function __construct(UriInterface $uri)
    {
        $this->reset();

        $this->uri = $uri;
    }


    public static function for(UriInterface $uri) : self
    {
        return new self($uri);
    }


    public function build() : Request
    {
        $request = new Request(
            $this->id ?: RequestId::generate(),
            $this->uri,
            $this->issueInstant ?: Instant::now(),
            $this->binding ?: Binding::httpRedirect(),
            $this->forceAuthn,
            $this->passive,
            $this->requestedAuthnContext,
            $this->nameIdPolicy,
            $this->relayState,
            $this->providerName,
            $this->parameters
        );

        $this->reset();

        return $request;
    }


    public function id(RequestId $id) : self
    {
        $this->id = $id;

        return $this;
    }


    public function issueInstant(Instant $issueInstant) : self
    {
        $this->issueInstant = $issueInstant;

        return $this;
    }


    public function parameters(array $parameters) : self
    {
        $this->parameters += $parameters;

        return $this;
    }


    public function forceAuthn(bool $forceAuthn = true) : self
    {
        $this->forceAuthn = $forceAuthn;

        return $this;
    }


    public function passive(bool $passive = true) : self
    {
        $this->passive = $passive;

        return $this;
    }


    public function requestedAuthnContext(RequestedAuthnContext $requestedAuthnContext) : self
    {
        $this->requestedAuthnContext = $requestedAuthnContext;

        return $this;
    }


    public function appendAuthnContextClass(AuthnContextClass $class) : self
    {
        if (null === $this->requestedAuthnContext) {
            $this->requestedAuthnContext = RequestedAuthnContext::exact();
        }

        $this->requestedAuthnContext = $this->requestedAuthnContext->withAppendedContextClass($class);

        return $this;
    }


    public function nameIdPolicy(NameIdPolicy $nameIdPolicy) : self
    {
        $this->nameIdPolicy = $nameIdPolicy;

        return $this;
    }


    public function providerName(Name $providerName) : self
    {
        $this->providerName = $providerName;

        return $this;
    }


    public function providerNameFromOrganisation(Organisation $organisation) : self
    {
        $this->providerName = $organisation->displayName();

        return $this;
    }


    public function relayState(string $state) : self
    {
        $this->relayState = $state;

        return $this;
    }


    private function reset() : void
    {
        $this->id                    = null;
        $this->uri                   = null;
        $this->issueInstant          = null;
        $this->parameters            = [];
        $this->forceAuthn            = false;
        $this->passive               = false;
        $this->requestedAuthnContext = null;
        $this->nameIdPolicy          = null;
        $this->providerName          = null;
    }
}
