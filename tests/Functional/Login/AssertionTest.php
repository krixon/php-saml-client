<?php

namespace Krixon\SamlClient\Test\Functional\Login;

use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Login\Response;
use Krixon\SamlClient\Protocol\Attribute;
use Krixon\SamlClient\Protocol\AttributeNameFormat;
use Krixon\SamlClient\Security\Key;
use Krixon\SamlClient\Test\TestCase;

class AssertionTest extends TestCase
{
    /**
     * @dataProvider extractsAttributesProvider
     *
     * @param string      $xmlFile
     * @param array       $expected
     * @param string|null $keyFile
     *
     * @throws \Exception
     */
    public function testExtractsAttributes(string $xmlFile, array $expected, string $keyFile = null)
    {
        $key = null;
        if ($keyFile) {
            $key = new Key($this->fixtureContent($keyFile));
        }

        $xml       = $this->fixtureContent($xmlFile);
        $document  = SamlDocument::import($xml);
        $response  = Response::fromDocument($document, $key);

        static::assertEquals($expected, $response->attributes());
    }


    public function extractsAttributesProvider() : array
    {
        $standardAttributes = [
            new Attribute(
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                ['karlrixon@gmail.com'],
                AttributeNameFormat::uriReference()
            ),
            new Attribute(
                'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
                ['Karl Rixon'],
                AttributeNameFormat::uriReference()
            ),
        ];

        return [
            [
                'assertions/unsigned-unencrypted.xml',
                $standardAttributes
            ],
            [
                'assertions/unsigned-encrypted.xml',
                $standardAttributes,
                '512b-rsa-example-keypair.pem'
            ],
            [
                'assertions/no-attribute-value.xml',
                [
                    new Attribute(
                        'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                        ['karlrixon@gmail.com'],
                        AttributeNameFormat::uriReference()
                    ),
                    new Attribute(
                        'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
                        [],
                        AttributeNameFormat::uriReference()
                    ),
                ]
            ],
            [
                'assertions/signed-assertion-unencrypted.xml',
                $standardAttributes
            ],
            [
                'assertions/signed-message-unencrypted.xml',
                $standardAttributes
            ],
            [
                'assertions/signed-both-unencrypted.xml',
                $standardAttributes
            ],
            [
                'assertions/signed-assertion-encrypted.xml',
                $standardAttributes,
                '512b-rsa-example-keypair.pem'
            ],
            [
                'assertions/signed-message-encrypted.xml',
                $standardAttributes,
                '512b-rsa-example-keypair.pem'
            ],
            [
                'assertions/signed-both-encrypted.xml',
                $standardAttributes,
                '512b-rsa-example-keypair.pem'
            ],
        ];
    }


    public function testConvertsValuesToInternalTypes()
    {
        $xml        = $this->fixtureContent('assertions/typed-attributes.xml');
        $document   = SamlDocument::import($xml);
        $response   = Response::fromDocument($document);
        $attributes = $response->attributes();

        static::assertCount(22, $attributes);

        static::assertSame('karlrixon@gmail.com', $attributes[0]->firstValue()); // xs:string
        static::assertSame(123456789, $attributes[1]->firstValue());             // xs:int (positive)
        static::assertSame(-123456789, $attributes[2]->firstValue());            // xs:int (negative)
        static::assertSame(123456789, $attributes[3]->firstValue());             // xs:integer (positive)
        static::assertSame(666, $attributes[4]->firstValue());                   // xs:positiveInteger
        static::assertSame(-666, $attributes[5]->firstValue());                  // xs:nonPositiveInteger
        static::assertSame(-666, $attributes[6]->firstValue());                  // xs:negativeInteger
        static::assertSame(666, $attributes[7]->firstValue());                   // xs:nonNegativeInteger
        static::assertSame(100, $attributes[8]->firstValue());                   // xs:short (positive)
        static::assertSame(-100, $attributes[9]->firstValue());                  // xs:short (negative)
        static::assertSame(255, $attributes[10]->firstValue());                  // xs:unsignedShort
        static::assertSame(12345678987654321, $attributes[11]->firstValue());    // xs:long (positive)
        static::assertSame(-12345678987654321, $attributes[12]->firstValue());   // xs:long (negative)
        static::assertSame(12345678987654321, $attributes[13]->firstValue());    // xs:unsignedLong
        static::assertSame(5, $attributes[14]->firstValue());                    // xs:byte (positive)
        static::assertSame(-5, $attributes[15]->firstValue());                   // xs:byte (negative)
        static::assertSame(5, $attributes[16]->firstValue());                    // xs:unsignedByte
        static::assertSame(42.01, $attributes[17]->firstValue());                // xs:decimal
        static::assertSame(42.01, $attributes[18]->firstValue());                // xs:float
        static::assertSame(42.01, $attributes[19]->firstValue());                // xs:double

        // xs:dateTime
        /** @var \DateTimeImmutable $datetime */
        $datetime = $attributes[20]->firstValue();
        static::assertInstanceOf(\DateTimeImmutable::class, $datetime);
        static::assertSame(946728930, $datetime->getTimestamp()); // 2000-01-01T12:15:30Z

        // xs:dateTime with UTC offset
        /** @var \DateTimeImmutable $datetime */
        $datetime = $attributes[21]->firstValue();
        static::assertInstanceOf(\DateTimeImmutable::class, $datetime);
        static::assertSame(946710930, $datetime->getTimestamp()); // 2000-01-01T12:15:30+05:00, 2000-01-01T07:15:30Z
    }
}
