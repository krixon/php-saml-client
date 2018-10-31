<?php

namespace Krixon\SamlClient\Test\Unit\Protocol;

use Krixon\SamlClient\Protocol\NameFormat;
use Krixon\SamlClient\Test\Unit\TestCase;

class NameFormatTest extends TestCase
{
    public function testCanConstructEncrypted()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted',
            NameFormat::encrypted()->toString()
        );
    }


    public function testCanConstructEmailAddress()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
            NameFormat::emailAddress()->toString()
        );
    }


    public function testCanConstructEntity()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:entity',
            NameFormat::entity()->toString()
        );
    }


    public function testCanConstructKerberos()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:kerberos',
            NameFormat::kerberos()->toString()
        );
    }


    public function testCanConstructPersistent()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
            NameFormat::persistent()->toString()
        );
    }


    public function testCanConstructTransient()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
            NameFormat::transient()->toString()
        );
    }


    public function testCanConstructUnspecified()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            NameFormat::unspecified()->toString()
        );
    }


    public function testCanConstructWindowsDomainQualifiedName()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:WindowsDomainQualifiedName',
            NameFormat::windowsDomainQualifiedName()->toString()
        );
    }


    public function testCanConstructX509SubjectName()
    {
        static::assertSame(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:X509SubjectName',
            NameFormat::x509SubjectName()->toString()
        );
    }
}
