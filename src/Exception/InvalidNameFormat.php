<?php

namespace Krixon\SamlClient\Exception;

class InvalidNameFormat extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(string $format)
    {
        parent::__construct("Unknown or unsupported NameFormat '$format'.");
    }
}
