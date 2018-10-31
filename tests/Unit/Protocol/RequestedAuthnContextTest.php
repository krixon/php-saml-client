<?php

namespace Krixon\SamlClient\Test\Unit\Protocol;


use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\Test\Unit\TestCase;

class RequestedAuthnContextTest extends TestCase
{
    public function testUsesDefaultClassIfNoneSpecified()
    {
        $authnContext = RequestedAuthnContext::exact();
        $expected     = /** @lang XML */'
            <samlp:RequestedAuthnContext Comparison="exact">
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
            </samlp:RequestedAuthnContext>';

        static::assertElementProducesExpectedXml($expected, $authnContext);
    }


    public function testDeDuplicatesContextClasses()
    {
        $authnContext = RequestedAuthnContext
            ::exact()
            ->withAppendedContextClass(AuthnContextClass::kerberos())
            ->withAppendedContextClass(AuthnContextClass::kerberos());

        $expected     = /** @lang XML */'
            <samlp:RequestedAuthnContext Comparison="exact">
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos</saml:AuthnContextClassRef>
            </samlp:RequestedAuthnContext>';

        static::assertElementProducesExpectedXml($expected, $authnContext);
    }


    public function testSetsExactComparison()
    {
        $authnContext = RequestedAuthnContext::exact();
        $expected     = /** @lang XML */'
            <samlp:RequestedAuthnContext Comparison="exact">
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
            </samlp:RequestedAuthnContext>';

        static::assertElementProducesExpectedXml($expected, $authnContext);
    }


    public function testSetsBetterComparison()
    {
        $authnContext = RequestedAuthnContext::better();
        $expected     = /** @lang XML */'
            <samlp:RequestedAuthnContext Comparison="better">
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
            </samlp:RequestedAuthnContext>';

        static::assertElementProducesExpectedXml($expected, $authnContext);
    }


    public function testSetsMinimumComparison()
    {
        $authnContext = RequestedAuthnContext::minimum();
        $expected     = /** @lang XML */'
            <samlp:RequestedAuthnContext Comparison="minimum">
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
            </samlp:RequestedAuthnContext>';

        static::assertElementProducesExpectedXml($expected, $authnContext);
    }


    public function testSetsMaximumComparison()
    {
        $authnContext = RequestedAuthnContext::maximum();
        $expected     = /** @lang XML */'
            <samlp:RequestedAuthnContext Comparison="maximum">
                <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef>
            </samlp:RequestedAuthnContext>';

        static::assertElementProducesExpectedXml($expected, $authnContext);
    }
}
