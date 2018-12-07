<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Protocol\Binding;

/**
 * An instruction on how to carry out a login request to an IDP.
 */
class RequestInstruction
{
    private $request;
    private $payload;
    private $uri;


    public function __construct(Request $request, string $payload)
    {
        $this->request = $request;
        $this->payload = $payload;
    }


    public function uri() : string
    {
        if (null !== $this->uri) {
            return $this->uri;
        }

        $uri = $this->request->uri();

        if ($this->binding()->isHttpRedirect()) {
            // Build a full URL including query string. This is not done for HTTP-Post binding as that involves
            // POSTing the payload to the base URL via a HTML form with individual inputs.
            $additionalQuery = http_build_query($this->parameters(), null, '&', PHP_QUERY_RFC3986);
            $existingQuery   = $uri->getQuery();
            $separator       = $existingQuery ? '&' : '';
            $uri             = $uri->withQuery($existingQuery . $separator . $additionalQuery);
        }

        return $this->uri = $uri->__toString();
    }


    public function binding() : Binding
    {
        return $this->request->binding();
    }


    public function request() : string
    {
        return $this->payload;
    }


    public function relayState() : ?string
    {
        return $this->request->relayState();
    }


    private function parameters() : array
    {
        $parameters = ['SAMLRequest' => $this->payload];

        $relayState = $this->relayState();
        if (null !== $relayState) {
            $parameters['RelayState'] = $relayState;
        }

        return $parameters + $this->request->parameters();
    }
}