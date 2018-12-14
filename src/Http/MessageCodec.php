<?php

namespace Krixon\SamlClient\Http;

use Krixon\SamlClient\Document\SamlDocument;

/**
 * Responsible for converting a SamlDocument into a string suitable for use in a protocol message, and vice versa.
 */
interface MessageCodec
{
    /**
     * Returns the identifier for this encoding. This must be a URI.
     *
     * The identifier is used to populate the SAMLEncoding parameter when the HTTP-Redirect binding is used.
     *
     * @return string
     */
    public function id() : string;


    /**
     * Encodes a document into a string suitable for use in a HTTP message.
     *
     * @param SamlDocument $document
     *
     * @return string
     */
    public function encode(SamlDocument $document) : string;


    /**
     * Decodes a string from a HTTP message into a document.
     *
     * @param string $payload
     *
     * @return SamlDocument
     */
    public function decode(string $payload) : SamlDocument;
}