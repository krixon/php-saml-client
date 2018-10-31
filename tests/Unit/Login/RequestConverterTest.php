<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Compression\Deflator;
use Krixon\SamlClient\Compression\ZlibInflatorDeflator;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Login\RequestConverter;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\RequestId;
use Krixon\SamlClient\Test\Unit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class RequestConverterTest extends TestCase
{
    /**
     * @var Deflator|MockObject
     */
    private $deflator;


    public function setUp()
    {
        parent::setUp();

        $this->deflator = $this->createMock(Deflator::class);
    }


    public function testCanConstructWithoutSpecificDeflator()
    {
        static::assertInstanceOf(RequestConverter::class, new RequestConverter());
    }


    public function testCanConstructWithSpecificDeflator()
    {
        $requestConverter = new RequestConverter(new ZlibInflatorDeflator());

        static::assertInstanceOf(RequestConverter::class, $requestConverter);
    }


    public function testProducesExpectedConvertedString()
    {
        $requestConverter = new RequestConverter($this->deflator);
        $id               = RequestId::generate();
        $instant          = '2000-01-01T00:00:00Z';

        $request = RequestBuilder
            ::for($this->createUri())
            ->id($id)
            ->issueInstant(Instant::fromString($instant))
            ->build();

        $this->deflator->method('deflate')->willReturnArgument(0);

        $result   = base64_decode($requestConverter->convert($request));
        $expected = sprintf(/** @lang XML */'
            <samlp:AuthnRequest
              xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
              Version="2.0"
              ID="%s"
              IssueInstant="%s"
            />',
            $request->id()->toString(),
            $instant
        );

        static::assertXmlStringEqualsXmlString($expected, $result);
    }
}
