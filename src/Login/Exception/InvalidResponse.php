<?php

namespace Krixon\SamlClient\Login\Exception;

use Krixon\SamlClient\Document\Exception\InvalidDocument;
use Krixon\SamlClient\Exception\SamlClientException;

class InvalidResponse extends \DomainException implements SamlClientException
{
    public function __construct(string $message, \Throwable $cause = null)
    {
        parent::__construct($message, 0, $cause);
    }


    public static function invalidDocument(InvalidDocument $e)
    {
        return new self('Invalid document: ' . $e->getMessage(), $e);
    }
}