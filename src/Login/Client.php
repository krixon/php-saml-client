<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Exception\UnsupportedBinding;
use Krixon\SamlClient\Http\Deflate;
use Krixon\SamlClient\Http\MessageCodec;
use Krixon\SamlClient\Protocol\Binding;
use Krixon\SamlClient\Security\Key;
use Psr\Http\Message\ServerRequestInterface;

class Client
{
    private $messageCodec;


    public function __construct(MessageCodec $messageCodec = null)
    {
        $this->messageCodec = $messageCodec ?: new Deflate();
    }


    /**
     * Processes a login request and returns an instruction indicating how to make the next HTTP request to the IdP.
     */
    public function login(Request $request) : RequestInstruction
    {
        $binding = $request->binding();

        if ($binding->isHttpRedirect()) {
            return $this->httpRedirect($request);
        } elseif ($binding->isHttpPost()) {
            return $this->httpPost($request);
        }

        throw new UnsupportedBinding($binding, 'Authn request', Binding::httpPost(), Binding::httpRedirect());
    }


    public function consume(ServerRequestInterface $response, Key $decryptionKey = null) : Response
    {
        $payload = $response->getParsedBody()['SAMLResponse'] ?? null;

        if (!$payload) {
            // Currently only HTTP-POST is supported for authn responses, so if there was nothing provided
            // in the request body, assume a different binding was used.
            throw new UnsupportedBinding(null, 'Authn response', Binding::httpPost());
        }

        $document = $this->messageCodec->decode($payload);

        return Response::fromDocument($document, $decryptionKey);
    }


    private function httpRedirect(Request $request) : RequestInstruction
    {
        $uri        = $request->uri();
        $payload    = $this->messageCodec->encode($request->toDocument());
        $signature  = $request->signature();
        $parameters = [];

        // Note: Parameter order is important because they form the signed data.

        $parameters['SAMLRequest'] = $payload;

        if ($request->hasRelayState()) {
            $parameters['RelayState'] = $request->relayState();
        }

        if ($signature) {
            // For HTTP-Redirect, the request is stripped of an embedded sig and the URL query string itself is signed.
            // There is no need to explicitly strip the embedded sig here because Request will not have added that
            // element if the HTTP-Redirect binding is used. At this stage, we just need to build and sign the final
            // payload.
            //
            // See https://www.oasis-open.org/committees/download.php/56780/sstc-saml-bindings-errata-2.0-wd-06-diff.pdf
            // Section 3.4.4.1
            //
            // Note that although this process technically only applies to the DEFLATE encoding, no other encodings
            // are defined (custom ones are allowed) so the same approach is used regardless. In future this might
            // be refactored so that the encoder is responsible for producing the RequestInstruction which would
            // allow custom encodings to be plugged in.

            $parameters['SigAlg'] = $signature->algorithm()->toString();

            $data = array_map($signature->useRfc3986() ? 'rawurlencode' : 'urlencode', $parameters);
            $data = implode('&', $data);

            $sig = $signature->sign($data);
            $sig = base64_encode($sig);

            $parameters['Signature'] = $sig;
        }

        $parameters += $request->parameters();

        $additionalQuery = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
        $existingQuery   = $uri->getQuery();
        $separator       = $existingQuery ? '&' : '';
        $uri             = $uri->withQuery($existingQuery . $separator . $additionalQuery);

        return new RequestInstruction($uri, $payload, $request->binding());
    }


    private function httpPost(Request $request) : RequestInstruction
    {
        $payload = $this->messageCodec->encode($request->toDocument());

        return new RequestInstruction($request->uri(), $payload, $request->binding());
    }
}
