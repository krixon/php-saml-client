<?php

namespace Krixon\SamlClient\Test\Unit\Protocol;

use Krixon\SamlClient\Protocol\NameIdFormat;
use Krixon\SamlClient\Test\Unit\TestCase;

class NameFormatTest extends TestCase
{
    public function testCanConstructEncrypted()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted',
            NameIdFormat::encrypted()->toString()
        );
    }


    public function testCanConstructEmailAddress()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
            NameIdFormat::emailAddress()->toString()
        );
    }


    public function testCanConstructEntity()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:entity',
            NameIdFormat::entity()->toString()
        );
    }


    public function testCanConstructKerberos()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos',
            NameIdFormat::kerberos()->toString()
        );
    }


    public function testCanConstructPersistent()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
            NameIdFormat::persistent()->toString()
        );
    }


    public function testCanConstructTransient()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
            NameIdFormat::transient()->toString()
        );
    }


    public function testCanConstructUnspecified()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            NameIdFormat::unspecified()->toString()
        );
    }


    public function testCanConstructWindowsDomainQualifiedName()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName',
            NameIdFormat::windowsDomainQualifiedName()->toString()
        );
    }


    public function testCanConstructX509SubjectName()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName',
            NameIdFormat::x509SubjectName()->toString()
        );
    }
}
