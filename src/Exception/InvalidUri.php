<?php

namespace Krixon\SamlClient\Exception;

class InvalidUri extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(string $reason)
    {
        parent::__construct("Invalid URL: $reason.");
    }
}
