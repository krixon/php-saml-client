<?php

namespace Krixon\SamlClient\Protocol;

use Psr\Http\Message\UriInterface;

final class AssertionConsumerService
{
    private $uri;
    private $binding;


    public function __construct(UriInterface $uri = null, Binding $binding = null)
    {
        $this->uri     = $uri;
        $this->binding = $binding;
    }


    public function withUri(UriInterface $uri) : self
    {
        $instance = clone $this;

        $instance->uri = $uri;

        return $instance;
    }


    public function withBinding(Binding $binding) : self
    {
        $instance = clone $this;

        $instance->binding = $binding;

        return $instance;
    }


    public function setAttributesOn(\DOMElement $element) : void
    {
        if ($this->uri) {
            $element->setAttribute('AssertionConsumerServiceURL', $this->uri->__toString());
        }

        if ($this->binding) {
            $element->setAttribute('ProtocolBinding', $this->binding->toString());
        }
    }
}
