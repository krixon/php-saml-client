<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Compression\Deflator;
use Krixon\SamlClient\Compression\ZlibInflatorDeflator;

/**
 * Responsible for converting a Request object into a string suitable for use as a query string parameter.
 *
 * Note that the result is not URL-encoded; that is the responsibility of whatever builds the final URL.
 */
class RequestConverter
{
    private $deflator;


    public function __construct(Deflator $deflator = null)
    {
        $this->deflator = $deflator ?: new ZlibInflatorDeflator();
    }


    public function convert(Request $request) : string
    {
        $xml = self::stringify($request);
        $xml = $this->deflator->deflate($xml);

        return base64_encode($xml);
    }


    private static function stringify(Request $request) : string
    {
        $document = $request->toDomDocument();

        $document->formatOutput       = false;
        $document->preserveWhiteSpace = false;

        // Render the document element itself (ie the root node) as we don't want the XML declaration etc.
        return $document->saveXML($document->documentElement);
    }
}
