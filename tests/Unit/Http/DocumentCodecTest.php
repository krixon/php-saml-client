<?php

namespace Krixon\SamlClient\Test\Unit\Http;

use Krixon\SamlClient\Compression\InflatorDeflator;
use Krixon\SamlClient\Compression\ZlibInflatorDeflator;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Http\DocumentCodec;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\RequestId;
use Krixon\SamlClient\Test\Unit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DocumentCodecTest extends TestCase
{
    /**
     * @var InflatorDeflator|MockObject
     */
    private $inflatorDeflator;


    public function setUp()
    {
        parent::setUp();

        $this->inflatorDeflator = $this->createMock(InflatorDeflator::class);
    }


    public function testCanConstructWithoutSpecificInflatorDeflator()
    {
        static::assertInstanceOf(DocumentCodec::class, new DocumentCodec());
    }


    public function testCanConstructWithSpecificInflatorDeflator()
    {
        $requestConverter = new DocumentCodec(new ZlibInflatorDeflator());

        static::assertInstanceOf(DocumentCodec::class, $requestConverter);
    }


    public function testDocumentToPayload()
    {
        $documentCodec = new DocumentCodec($this->inflatorDeflator);
        $id            = $this->requestId();
        $instant       = '2000-01-01T00:00:00Z';

        $this->inflatorDeflator->method('deflate')->willReturnArgument(0);

        $request = RequestBuilder
            ::for($this->createUri())
            ->id($id)
            ->issueInstant(Instant::fromString($instant))
            ->build();

        $result   = $documentCodec->toPayload($request->toDocument());
        $expected =
            'PHNhbWxwOkF1dGhuUmVxdWVzdCB4bWxuczpzYW1scD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnByb3RvY29s' .
            'IiBWZXJzaW9uPSIyLjAiIElEPSJhYmMxMjMiIElzc3VlSW5zdGFudD0iMjAwMC0wMS0wMVQwMDowMDowMFoiLz4%3D';

        static::assertSame($expected, $result);
    }


    protected function requestId(string $id = 'abc123') : RequestId
    {
        $class    = new \ReflectionClass(RequestId::class);
        $instance = $class->newInstanceWithoutConstructor();

        $property = $class->getProperty('id');
        $property->setAccessible(true);

        $property->setValue($instance, $id);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $instance;
    }
}
