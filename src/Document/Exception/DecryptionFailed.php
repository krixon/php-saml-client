<?php

namespace Krixon\SamlClient\Document\Exception;

use Krixon\SamlClient\Exception\SamlClientException;

class DecryptionFailed extends \RuntimeException implements SamlClientException
{
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}