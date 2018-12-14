<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AssertionConsumerService;
use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Organisation;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
use Krixon\SamlClient\ServiceProvider;
use Krixon\SamlClient\Test\Unit\TestCase;

class RequestBuilderTest extends TestCase
{
    public function testWithDefaultConfiguration()
    {
        // Note: We have to specify at least an ID and Instant otherwise there's no easy way to verify the result
        // since these parameters are generated automatically otherwise.
        $request = $this->baseBuilder()->build();

        $expected = sprintf(/** @lang XML */'
           <samlp:AuthnRequest
             xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
             ID="%s"
             IssueInstant="2018-01-01T00:00:00Z"
             Version="2.0"
             Destination="http://example.com"
           />',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDocument());
    }


    public function testWithAllConfiguration()
    {
        $request = $this
            ->baseBuilder()
            ->forceAuthn()
            ->passive()
            ->nameIdPolicy(new NameIdPolicy('qualifier', null, true))
            ->requestedAuthnContext(RequestedAuthnContext::exact(AuthnContextClass::password()))
            ->appendAuthnContextClass(AuthnContextClass::kerberos())
            ->serviceProvider(new ServiceProvider('jmc', Name::fromString('Jupiter Mining Corp.')))
            ->build();

        $expected = sprintf(/** @lang XML */'
            <samlp:AuthnRequest
              xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
              xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
              ID="%s"
              IssueInstant="2018-01-01T00:00:00Z"
              Version="2.0"
              Destination="http://example.com"
              IsPassive="true"
              ForceAuthn="true"
              ProviderName="Jupiter Mining Corp."
            >
                <saml:Issuer>jmc</saml:Issuer>
                <samlp:NameIDPolicy AllowCreate="true" SPNameQualifier="qualifier"/>
                <samlp:RequestedAuthnContext Comparison="exact">
                    <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Password</saml:AuthnContextClassRef>
                    <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos</saml:AuthnContextClassRef>
                </samlp:RequestedAuthnContext>
            </samlp:AuthnRequest>',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDocument());
    }


    public function testCanAppendAuthnContextClassWithoutExistingRequestedAuthnContext()
    {
        $request = $this
            ->baseBuilder()
            ->appendAuthnContextClass(AuthnContextClass::kerberos())
            ->build();

        $expected = sprintf(/** @lang XML */'
            <samlp:AuthnRequest
             xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
             xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
             ID="%s"
             IssueInstant="2018-01-01T00:00:00Z"
             Version="2.0"
             Destination="http://example.com"
            >
                <samlp:RequestedAuthnContext Comparison="exact">
                    <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos</saml:AuthnContextClassRef>
                </samlp:RequestedAuthnContext>
            </samlp:AuthnRequest>',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDocument());
    }


    private function baseBuilder() : RequestBuilder
    {
        return RequestBuilder
            ::for($this->createUri())
            ->issueInstant(Instant::fromString('2018-01-01T00:00:00Z'));
    }
}
