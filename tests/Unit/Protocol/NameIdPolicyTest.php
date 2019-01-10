<?php

namespace Krixon\SamlClient\Test\Unit\Protocol;

use Krixon\SamlClient\Protocol\NameIdFormat;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Test\Unit\TestCase;

class NameIdPolicyTest extends TestCase
{
    public function testEmptyQualifierStringIsTreatedAsNull()
    {
        static::assertElementProducesExpectedXml('', new NameIdPolicy(''));
    }


    public function testTransientFormatForcesAllowCreateToBeFalse()
    {
        $policy   = new NameIdPolicy(null, NameIdFormat::transient(), true);
        $expected = '<samlp:NameIDPolicy Format="urn:oasis:names:tc:SAML:2.0:nameid-format:transient"/>';

        static::assertElementProducesExpectedXml($expected, $policy);
    }


    /**
     * @dataProvider producesExpectedXmlProvider
     */
    public function testProducesExpectedXml(NameIdPolicy $policy, string $expected) : void
    {
        static::assertElementProducesExpectedXml($expected, $policy);
    }


    public function producesExpectedXmlProvider() : array
    {
        return [
            'Defaults' => [
                new NameIdPolicy(),
                ''
            ],
            'Qualifier' => [
                new NameIdPolicy('qualifier'),
                '<samlp:NameIDPolicy SPNameQualifier="qualifier"/>'
            ],
            'Qualifier as empty string' => [
                new NameIdPolicy(''),
                ''
            ],
            'Format: Email address' => [
                new NameIdPolicy(null, NameIdFormat::emailAddress()),
                '<samlp:NameIDPolicy Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress"/>'
            ],
            'Allow create: true' => [
                new NameIdPolicy(null, null, true),
                '<samlp:NameIDPolicy AllowCreate="true"/>'
            ],
            'Allow create: false' => [
                new NameIdPolicy(null, null, false),
                ''
            ],
            'Everything set explicitly' => [
                new NameIdPolicy('qualifier', NameIdFormat::emailAddress(), true),
                '<samlp:NameIDPolicy ' .
                '  Format="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress" ' .
                '  AllowCreate="true" ' .
                '  SPNameQualifier="qualifier"' .
                '/>'
            ],
        ];
    }
}
