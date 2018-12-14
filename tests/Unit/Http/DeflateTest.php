<?php

namespace Krixon\SamlClient\Test\Unit\Http;

use Krixon\SamlClient\Http\Deflate;
use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\RequestId;
use Krixon\SamlClient\Test\Unit\TestCase;

class DeflateTest extends TestCase
{
    public function testCanConstruct()
    {
        static::assertInstanceOf(Deflate::class, new Deflate());
    }


    public function testEncode()
    {
        $codec   = new Deflate();
        $id      = $this->requestId();
        $instant = '2000-01-01T00:00:00Z';

        $request = RequestBuilder
            ::for($this->createUri())
            ->id($id)
            ->issueInstant(Instant::fromString($instant))
            ->build();

        $result   = $codec->encode($request->toDocument());
        $expected =
            'HY3BCsIwEER/JezdNq23xQqFXgp6UfHgLYaFFpJN7G6gn28ovMMwD2Yu4mLIOBZd+EG/QqJmj4EFDzFA2RiTk1WQXSRB9fgc7zfsG4t' .
            '5S5p8CmDetMmaeIBag5mnAdzXd/25ZpFCM4s61qqttSfbVV7W4sEHzFRfV3Z6LCyqGduWdhdzoManCO31Dw==';

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
