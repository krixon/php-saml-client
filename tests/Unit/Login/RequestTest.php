<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Login\Request;

class RequestTest extends LoginTestCase
{
    /**
     * @dataProvider producesExpectedXmlProvider
     */
    public function testProducesExpectedXml(Request $request, string $expected)
    {
        static::assertXmlStringEqualsXmlString($expected, $request->toDomDocument());
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
                    />',
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
