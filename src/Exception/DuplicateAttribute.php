<?php

namespace Krixon\SamlClient\Exception;

class DuplicateAttribute extends \DomainException implements SamlClientException
{
    public function __construct(string $name, string $nameFormat = null, \Throwable $previous = null)
    {
        $message = "Duplicate attribute found with Name '$name'";

        if ($nameFormat) {
            $message .= " and NameFormat '$nameFormat'";
        }

        parent::__construct("$message.", 0, $previous);
    }
}