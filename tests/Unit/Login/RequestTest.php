<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Login\Request;
use Krixon\SamlClient\Name;
use Krixon\SamlClient\Protocol\AssertionConsumerService;
use Krixon\SamlClient\Protocol\Binding;
use Krixon\SamlClient\ServiceProvider;

class RequestTest extends LoginTestCase
{
    /**
     * @dataProvider producesExpectedXmlProvider
     */
    public function testProducesExpectedXml(Request $request, string $expected)
    {
        static::assertXmlStringEqualsXmlString($expected, $request->toDocument());
    }


    public function producesExpectedXmlProvider() : array
    {
        return [
            [
                $request = self::baseBuilder()->build(),

                sprintf(/** @lang XML */'
                    <samlp:AuthnRequest
                      xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                      Version="2.0"
                      ID="%s"
                      IssueInstant="2018-01-01T00:00:00Z"
                      Destination="http://example.com"
                    />',
                    $request->id()->toString()
                )
            ],
            'Empty ACS does not add ACS properties' => [

                $request = self::baseBuilder()
                    ->serviceProvider(new ServiceProvider(
                        'jmc',
                        Name::fromString('Jupiter Mining Corp'),
                        new AssertionConsumerService()
                    ))
                    ->build(),

                sprintf(/** @lang XML */'
                    <samlp:AuthnRequest
                      xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                      xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
                      Version="2.0"
                      ID="%s"
                      IssueInstant="2018-01-01T00:00:00Z"
                      Destination="http://example.com"
                      ProviderName="Jupiter Mining Corp"
                    >
                        <saml:Issuer>jmc</saml:Issuer>
                    </samlp:AuthnRequest>',
                    $request->id()->toString()
                )
            ],
            'ACS with URL only' => [

                $request = self::baseBuilder()
                    ->serviceProvider(new ServiceProvider(
                        'jmc',
                        Name::fromString('Jupiter Mining Corp'),
                        new AssertionConsumerService($this->createUri())
                    ))
                    ->build(),

                sprintf(/** @lang XML */'
                    <samlp:AuthnRequest
                      xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                      xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
                      Version="2.0"
                      ID="%s"
                      IssueInstant="2018-01-01T00:00:00Z"
                      Destination="http://example.com"
                      ProviderName="Jupiter Mining Corp"
                      AssertionConsumerServiceURL="http://example.com"
                    >
                        <saml:Issuer>jmc</saml:Issuer>
                    </samlp:AuthnRequest>',
                    $request->id()->toString()
                )
            ],
            'ACS with binding only' => [

                $request = self::baseBuilder()
                    ->serviceProvider(new ServiceProvider(
                        'jmc',
                        Name::fromString('Jupiter Mining Corp'),
                        new AssertionConsumerService(null, Binding::httpPost())
                    ))
                    ->build(),

                sprintf(/** @lang XML */'
                    <samlp:AuthnRequest
                      xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                      xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
                      Version="2.0"
                      ID="%s"
                      IssueInstant="2018-01-01T00:00:00Z"
                      Destination="http://example.com"
                      ProviderName="Jupiter Mining Corp"
                      ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
                    >
                        <saml:Issuer>jmc</saml:Issuer>
                    </samlp:AuthnRequest>',
                    $request->id()->toString()
                )
            ],
            'ACS with binding and URL' => [

                $request = self::baseBuilder()
                    ->serviceProvider(new ServiceProvider(
                        'jmc',
                        Name::fromString('Jupiter Mining Corp'),
                        new AssertionConsumerService($this->createUri(), Binding::httpPost())
                    ))
                    ->build(),

                sprintf(/** @lang XML */'
                    <samlp:AuthnRequest
                      xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                      xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion"
                      Version="2.0"
                      ID="%s"
                      IssueInstant="2018-01-01T00:00:00Z"
                      Destination="http://example.com"
                      ProviderName="Jupiter Mining Corp"
                      AssertionConsumerServiceURL="http://example.com"
                      ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
                    >
                        <saml:Issuer>jmc</saml:Issuer>
                    </samlp:AuthnRequest>',
                    $request->id()->toString()
                )
            ],
        ];
    }


    public function testExposesParameters() : void
    {
        $parameters = ['foo' => 'bar', 'baz' => 123];
        $request    = $this->baseBuilder()->parameters($parameters)->build();

        static::assertSame($parameters, $request->parameters());
    }


    public function testExposesRelayState() : void
    {
        $state   = 'some custom opaque value';
        $request = $this->baseBuilder()->relayState($state)->build();

        static::assertSame($state, $request->relayState());
    }
}
