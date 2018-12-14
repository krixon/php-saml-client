<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use GuzzleHttp\Psr7\Uri;
use Krixon\SamlClient\Http\MessageCodec;
use Krixon\SamlClient\Login\Client;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Login\RequestInstruction;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends LoginTestCase
{
    /**
     * @var MessageCodec|MockObject
     */
    private $messageCodec;


    protected function setUp()
    {
        parent::setUp();

        $this->messageCodec = $this->createMock(MessageCodec::class);
    }


    public function testCanBeConstructed()
    {
        static::assertInstanceOf(Client::class, new Client($this->messageCodec));
    }


    public function testProducesRequestInstruction()
    {
        $this->messageCodec->method('encode')->willReturn('converted request');

        $client  = new Client($this->messageCodec);
        $request = RequestBuilder::for(new Uri('http://example.com'))->build();

        $instruction = $client->login($request);

        static::assertInstanceOf(RequestInstruction::class, $instruction);
    }
}
