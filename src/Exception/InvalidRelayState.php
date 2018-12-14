<?php

namespace Krixon\SamlClient\Exception;

final class InvalidRelayState extends \InvalidArgumentException implements SamlClientException
{
    /** @noinspection PhpHierarchyChecksInspection LSP does not apply to constructors! */
    private function __construct(string $message)
    {
        parent::__construct($message);
    }


    public static function tooLong() : self
    {
        return new self('RelayState must not exceed 80 bytes.');
    }
}