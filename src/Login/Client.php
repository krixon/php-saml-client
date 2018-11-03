<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Exception\UnsupportedBinding;
use Krixon\SamlClient\Http\HttpFactory;
use Krixon\SamlClient\Protocol\Binding;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $requestConverter;
    private $httpFactory;


    public function __construct(HttpFactory $httpFactory, RequestConverter $requestConverter = null)
    {
        $this->httpFactory      = $httpFactory;
        $this->requestConverter = $requestConverter ?: new RequestConverter();
    }


    /**
     * Processes a login request and returns an HTTP message.
     *
     * If a RequestInterface is returned, a server-side HTTP client should ultimately issue that request. In practice,
     * this will be a POST request to the identity provider containing the login request payload. A RequestInterface
     * will be returned if the HTTP-POST binding is configured for the request.
     *
     * If a ResponseInterface is returned, it should be presented to the user agent which initiated login. In practice,
     * this will be a 302 redirect response with a Location header containing the identity provider's URL along
     * with the login request payload parameters. A ResponseInterface will be returned if the HTTP-Redirect binding
     * is configured for the request.
     */
    public function login(Request $request) : MessageInterface
    {
        $binding    = $request->binding();
        $uri        = $request->uri();
        $parameters = $this->parameters($request);

        // TODO: Signing.

        switch (true) {
            case $binding->isHttpPost():
                return $this->postMessage($uri, $parameters);
            case $binding->isHttpRedirect():
                return $this->redirectMessage($uri, $parameters);
        }

        throw new UnsupportedBinding($binding, 'login', [Binding::httpPost(), Binding::httpRedirect()]);
    }


    private function parameters(Request $request) : array
    {
        $parameters = $request->parameters();

        $parameters['SAMLRequest'] = $this->requestConverter->convert($request);

        $relayState = $request->relayState();
        if (null !== $relayState) {
            $parameters['RelayState'] = $relayState;
        }

        return $parameters;
    }


    private function redirectMessage(UriInterface $uri, array $payload) : ResponseInterface
    {
        $additionalQuery = $this->buildQuery($payload);
        $existingQuery   = $uri->getQuery();
        $separator       = $existingQuery ? '&' : '';

        $uri = $uri->withQuery($existingQuery . $separator . $additionalQuery);

        return $this
            ->httpFactory
            ->createResponse()
            ->withStatus(302)
            ->withHeader('Location', (string)$uri);
    }


    private function postMessage(UriInterface $uri, array $payload) : RequestInterface
    {
        $body   = $this->buildQuery($payload);
        $stream = $this->httpFactory->createStream($body);

        return $this
            ->httpFactory
            ->createRequest('POST', $uri)
            ->withBody($stream);
    }


    private function buildQuery(array $payload) : string
    {
        return http_build_query($payload, null, '&', PHP_QUERY_RFC3986);
    }
}
