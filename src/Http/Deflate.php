<?php

namespace Krixon\SamlClient\Http;

use Krixon\SamlClient\Document\SamlDocument;

final class Deflate implements MessageCodec
{
    private const ID = 'urn:oasis:names:tc:SAML:2.0:bindings:URL-Encoding:DEFLATE';


    public function id() : string
    {
        return self::ID;
    }


    public function encode(SamlDocument $document) : string
    {
        $xml = gzdeflate($document->toString());

        return base64_encode($xml);
    }


    public function decode(string $payload) : SamlDocument
    {
        $xml = base64_decode($payload);
        $xml = gzinflate($xml);

        return SamlDocument::import($xml);
    }
}