<?php

namespace Krixon\SamlClient\Document\Exception;

use Krixon\SamlClient\Exception\SamlClientException;

class InvalidDocument extends \InvalidArgumentException implements SamlClientException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }


    public static function unsupportedSamlVersion(string $version) : self
    {
        return new self("Unsupported SAML version $version. Only version 2.0 is supported.");
    }


    public static function requiredElementNotPresent(string $element) : self
    {
        return new self(sprintf('Required element <%s> is not present.', $element));
    }
}
