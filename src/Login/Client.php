<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Exception\UnsupportedBinding;
use Krixon\SamlClient\Http\HttpFactory;
use Krixon\SamlClient\Http\DocumentCodec;
use Krixon\SamlClient\Protocol\Binding;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private $documentCodec;
    private $httpFactory;


    public function __construct(HttpFactory $httpFactory, DocumentCodec $requestConverter = null)
    {
        $this->httpFactory   = $httpFactory;
        $this->documentCodec = $requestConverter ?: new DocumentCodec();
    }


    /**
     * Processes a login request and returns an HTTP message.
     *
     * The returned ResponseInterface should be presented to the user agent which initiated login. In practice,
     * this will be a 302 redirect response with a Location header containing the identity provider's URL along
     * with the login request payload parameters. A ResponseInterface will be returned if the HTTP-Redirect binding
     * is configured for the request.
     */
    public function redirect(Request $request) : ResponseInterface
    {
        $binding    = $request->binding();
        $uri        = $request->uri();
        $parameters = $this->parameters($request);

        // TODO: Signing.

        if (!$binding->isHttpRedirect()) {
            // Currently only HTTP-Redirect binding is supported for authn requests.
            throw new UnsupportedBinding($binding, 'login', Binding::httpRedirect());
        }

        return $this->redirectResponse($uri, $parameters);
    }


    public function consume(ServerRequestInterface $response) : Response
    {
        $payload = $response->getParsedBody()['SAMLResponse'] ?? null;

        if (!$payload) {
            // Currently only HTTP-POST is supported for authn responses, so if there was nothing provided
            // in the request body, assume a different binding was used.
            throw new UnsupportedBinding(null, 'Authn response', Binding::httpPost());
        }

        $document = $this->documentCodec->fromPayload($payload);

        return Response::fromDocument($document);
    }


    private function parameters(Request $request) : array
    {
        $parameters = $request->parameters();

        $parameters['SAMLRequest'] = $this->documentCodec->toPayload($request->toDocument());

        $relayState = $request->relayState();
        if (null !== $relayState) {
            $parameters['RelayState'] = $relayState;
        }

        return $parameters;
    }


    private function redirectResponse(UriInterface $uri, array $payload) : ResponseInterface
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


    private function buildQuery(array $payload) : string
    {
        return http_build_query($payload, null, '&', PHP_QUERY_RFC3986);
    }
}
