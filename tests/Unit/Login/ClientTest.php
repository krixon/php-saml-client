<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use GuzzleHttp\Psr7\Uri;
use Krixon\SamlClient\Login\Client;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Http\DocumentCodec;
use Krixon\SamlClient\Login\RequestInstruction;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends LoginTestCase
{
    /**
     * @var DocumentCodec|MockObject
     */
    private $documentCodec;


    protected function setUp()
    {
        parent::setUp();

        $this->documentCodec = $this->createMock(DocumentCodec::class);
    }


    public function testCanBeConstructed()
    {
        static::assertInstanceOf(Client::class, new Client($this->documentCodec));
    }


    public function testProducesRequestInstruction()
    {
        $this->documentCodec->method('toPayload')->willReturn('converted request');

        $client  = new Client($this->documentCodec);
        $request = RequestBuilder::for(new Uri('http://example.com'))->build();

        $instruction = $client->login($request);

        static::assertInstanceOf(RequestInstruction::class, $instruction);
    }
}
