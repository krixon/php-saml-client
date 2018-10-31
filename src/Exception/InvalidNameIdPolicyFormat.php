<?php

namespace Krixon\SamlClient\Exception;

class InvalidNameIdPolicyFormat extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(string $format)
    {
        parent::__construct("Unknown or unsupported Format '$format'.");
    }
}
