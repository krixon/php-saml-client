<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Login\RequestInstruction;
use Krixon\SamlClient\Test\Unit\TestCase;

class RequestInstructionTest extends TestCase
{
    public function testReturnsUriWithRequestInQueryStringWithRedirectBinding()
    {
        $request = RequestBuilder
            ::for($this->createUri('http://example.com'))
            ->httpRedirectBinding()
            ->parameters(['foo' => 'bar', 'name' => 'rimmer'])
            ->relayState('some random state')
            ->build();

        $instruction = new RequestInstruction($request, 'payload');

        static::assertSame(
            'http://example.com?SAMLRequest=payload&RelayState=some%20random%20state&foo=bar&name=rimmer',
            $instruction->uri()
        );
    }
}