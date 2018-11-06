<?php

namespace Krixon\SamlClient\Http;

use Krixon\SamlClient\Compression\InflatorDeflator;
use Krixon\SamlClient\Compression\ZlibInflatorDeflator;
use Krixon\SamlClient\Document\SamlDocument;

/**
 * Responsible for converting a SamlDocument into a string suitable for use in a HTTP request, and vice versa.
 */
class DocumentCodec
{
    private $inflatorDeflator;


    public function __construct(InflatorDeflator $inflatorDeflator = null)
    {
        $this->inflatorDeflator = $inflatorDeflator ?: new ZlibInflatorDeflator();
    }


    public function toPayload(SamlDocument $document) : string
    {
        $xml = self::stringify($document);
        $xml = $this->inflatorDeflator->deflate($xml);
        $xml = base64_encode($xml);

        return rawurlencode($xml);
    }


    public function fromPayload(string $payload) : SamlDocument
    {
        $xml = rawurldecode($payload);
        $xml = base64_decode($xml);
        $xml = $this->inflatorDeflator->inflate($xml);

        return SamlDocument::import($xml);
    }


    private static function stringify(SamlDocument $document) : string
    {
        $document->formatOutput       = false;
        $document->preserveWhiteSpace = false;

        // Render the document element itself (ie the root node) as we don't want the XML declaration etc.
        return $document->saveXML($document->documentElement);
    }
}
