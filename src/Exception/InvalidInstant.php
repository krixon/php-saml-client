<?php

namespace Krixon\SamlClient\Exception;

class InvalidInstant extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(string $id)
    {
        parent::__construct("Invalid SAML instant: '$id'. Expected format is 'YYYY-MM-DDTHH:MM:SSZ'.");
    }
}
