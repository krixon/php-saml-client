<?php

namespace Krixon\SamlClient\Exception;

final class InvalidAssertion extends \DomainException implements SamlClientException
{
    /** @noinspection PhpHierarchyChecksInspection LSP does not apply to constructors! */
    private function __construct(string $message)
    {
        parent::__construct($message);
    }


    public static function missingId() : self
    {
        return new self('Assertion must have a non-empty ID attribute.');
    }
}