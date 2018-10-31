<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AuthnContextClass;
use Krixon\SamlClient\Config\Organisation;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Protocol\NameIdPolicy;
use Krixon\SamlClient\Protocol\RequestedAuthnContext;
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
           />',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDomDocument());
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
            ->providerName(Name::fromString('Jupiter Mining Corp.'))
            ->build();

        $expected = sprintf(/** @lang XML */'
            <samlp:AuthnRequest
              xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
              xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
              ID="%s"
              IssueInstant="2018-01-01T00:00:00Z"
              Version="2.0"
              IsPassive="true"
              ForceAuthn="true"
              ProviderName="Jupiter Mining Corp."
            >
                <samlp:NameIDPolicy AllowCreate="true" SPNameQualifier="qualifier"/>
                <samlp:RequestedAuthnContext Comparison="exact">
                    <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Password</saml:AuthnContextClassRef>
                    <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos</saml:AuthnContextClassRef>
                </samlp:RequestedAuthnContext>
            </samlp:AuthnRequest>',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDomDocument());
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
            >
                <samlp:RequestedAuthnContext Comparison="exact">
                    <saml:AuthnContextClassRef>urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos</saml:AuthnContextClassRef>
                </samlp:RequestedAuthnContext>
            </samlp:AuthnRequest>',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDomDocument());
    }


    public function testUsesOrganisationDisplayNameAsProviderName()
    {
        $organisation = new Organisation(
            'en-US',
            Name::fromString('Jupiter Mining Corp.'),
            Name::fromString('Jupiter Mining'),
            $this->createUri()
        );

        $request = self
            ::baseBuilder()
            ->providerNameFromOrganisation($organisation)
            ->build();

        $expected = sprintf(/** @lang XML */'
           <samlp:AuthnRequest
             xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
             ID="%s"
             IssueInstant="2018-01-01T00:00:00Z"
             Version="2.0"
             ProviderName="Jupiter Mining"
           />',
            $request->id()->toString()
        );

        static::assertXmlStringEqualsXmlString($expected, $request->toDomDocument());
    }


    private function baseBuilder() : RequestBuilder
    {
        return RequestBuilder
            ::for($this->createUri())
            ->issueInstant(Instant::fromString('2018-01-01T00:00:00Z'));
    }
}
