<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Exception\UnsupportedBinding;
use Krixon\SamlClient\Http\DocumentCodec;
use Krixon\SamlClient\Protocol\Binding;
use Psr\Http\Message\ServerRequestInterface;

class Client
{
    private $documentCodec;


    public function __construct(DocumentCodec $requestConverter = null)
    {
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
    public function login(Request $request) : RequestInstruction
    {
        return new RequestInstruction($request, $this->documentCodec->toPayload($request->toDocument()));
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
}
