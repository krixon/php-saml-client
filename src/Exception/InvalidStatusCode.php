<?php

namespace Krixon\SamlClient\Exception;

class InvalidStatusCode extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(string $code)
    {
        parent::__construct("Invalid StatusCode: '$code'.");
    }
}
