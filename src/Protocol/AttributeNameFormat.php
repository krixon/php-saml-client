<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Exception\InvalidNameFormat;

final class AttributeNameFormat
{
    private const BASIC         = 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic';
    private const UNSPECIFIED   = 'urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified';
    private const URI_REFERENCE = 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri';

    private const ENUM = [
        self::BASIC,
        self::UNSPECIFIED,
        self::URI_REFERENCE,
    ];

    private $format;


    private function __construct(string $format)
    {
        if (!in_array($format, self::ENUM, true)) {
            throw new InvalidNameFormat($format);
        }

        $this->format = $format;
    }


    public static function fromString(string $format)
    {
        return new self($format);
    }


    public static function basic() : self
    {
        return new self(self::BASIC);
    }


    public static function unspecified() : self
    {
        return new self(self::UNSPECIFIED);
    }


    public static function uriReference() : self
    {
        return new self(self::URI_REFERENCE);
    }


    public function toString() : string
    {
        return $this->format;
    }


    public function equals(self $other) : bool
    {
        return $this->format === $other->format;
    }
}
