<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Exception\InvalidNameFormat;

final class NameIdFormat
{
    private const ENCRYPTED         = 'urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted';
    private const EMAIL_ADDRESS     = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
    private const ENTITY            = 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity';
    private const KERBEROS          = 'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos';
    private const PERSISTENT        = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';
    private const TRANSIENT         = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';
    private const UNSPECIFIED       = 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';
    private const WINDOWS_DOMAIN_QN = 'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName';
    private const X509_SUBJECT_NAME = 'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName';

    private const ENUM = [
        self::ENCRYPTED,
        self::EMAIL_ADDRESS,
        self::ENTITY,
        self::KERBEROS,
        self::PERSISTENT,
        self::TRANSIENT,
        self::UNSPECIFIED,
        self::WINDOWS_DOMAIN_QN,
        self::X509_SUBJECT_NAME,
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


    public static function encrypted() : self
    {
        return new self(self::ENCRYPTED);
    }


    public static function emailAddress() : self
    {
        return new self(self::EMAIL_ADDRESS);
    }


    public static function entity() : self
    {
        return new self(self::ENTITY);
    }


    public static function kerberos() : self
    {
        return new self(self::KERBEROS);
    }


    public static function persistent() : self
    {
        return new self(self::PERSISTENT);
    }


    public static function transient() : self
    {
        return new self(self::TRANSIENT);
    }


    public static function unspecified() : self
    {
        return new self(self::UNSPECIFIED);
    }


    public static function windowsDomainQualifiedName() : self
    {
        return new self(self::WINDOWS_DOMAIN_QN);
    }


    public static function x509SubjectName() : self
    {
        return new self(self::X509_SUBJECT_NAME);
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
