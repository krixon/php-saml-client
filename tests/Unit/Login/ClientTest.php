<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use GuzzleHttp\Psr7\Uri;
use Krixon\SamlClient\Http\HttpFactory;
use Krixon\SamlClient\Login\Client;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Login\RequestConverter;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;

class ClientTest extends LoginTestCase
{
    /**
     * @var RequestConverter|MockObject
     */
    private $requestConverter;

    /**
     * @var HttpFactory|MockObject
     */
    private $messageFactory;


    protected function setUp()
    {
        parent::setUp();

        $this->requestConverter = $this->createMock(RequestConverter::class);
        $this->messageFactory   = $this->createMock(HttpFactory::class);
    }


    public function testCanBeConstructed()
    {
        static::assertInstanceOf(Client::class, new Client($this->messageFactory, $this->requestConverter));
    }


    public function testProducesCorrectMessage()
    {
        $expected = 'http://example.com?foo=bar&name=rimmer&SAMLRequest=converted%20request&RelayState=some%20random%20state';
        $response = $this->createMock(ResponseInterface::class);

        $this->requestConverter->method('convert')->willReturn('converted request');
        $this->messageFactory->method('createResponse')->willReturn($response);

        $response->expects($this->atLeastOnce())->method('withStatus')->with(302)->willReturnSelf();
        $response->expects($this->atLeastOnce())->method('withHeader')->with('Location', $expected)->willReturnSelf();

        $client  = new Client($this->messageFactory, $this->requestConverter);
        $request = RequestBuilder
            ::for(new Uri('http://example.com'))
            ->parameters(['foo' => 'bar', 'name' => 'rimmer'])
            ->relayState('some random state')
            ->build();

        $message = $client->login($request);

        static::assertInstanceOf(ResponseInterface::class, $message);
    }
}
