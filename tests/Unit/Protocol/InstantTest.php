<?php

namespace Krixon\SamlClient\Test\Unit\Protocol;

use Krixon\SamlClient\Exception\InvalidInstant;
use Krixon\SamlClient\Protocol\Instant;
use PHPUnit\Framework\TestCase;

class InstantTest extends TestCase
{
    public function testCanConstructCurrentInstant()
    {
        $now   = Instant::now();
        $delta = 2; // Allow 2 seconds difference to account for execution time.

        static::assertEquals(time(), $now->toDateTime()->getTimestamp(), '', $delta);
    }


    public function testSystemDefaultTimezoneHasNoEffectWhenConstructingCurrentInstance()
    {
        self::withDefaultTimezone('America/Chicago', function () {
            $this->testCanConstructCurrentInstant();
        });
    }


    /**
     * @dataProvider validStringInputProvider
     */
    public function testCanConstructFromString(string $input, string $expected = null)
    {
        static::assertSame($expected ?: $input, Instant::fromString($input)->toString());
    }


    /**
     * @dataProvider validStringInputProvider
     */
    public function testSystemDefaultTimezoneHasNoEffectWhenConstructingFromString(
        string $input,
        string $expected = null
    ) {
        self::withDefaultTimezone('America/Chicago', function () use ($input, $expected) {
            $this->testCanConstructFromString($input, $expected);
        });
    }


    public function validStringInputProvider() : array
    {
        return [
            ['1970-01-01T00:00:00Z'],
            ['1970-01-01T00:00:00.123Z', '1970-01-01T00:00:00Z'],
            ['1970-01-01T00:00:00.123456Z', '1970-01-01T00:00:00Z'],
        ];
    }


    /**
     * @dataProvider invalidStringInputProvider
     */
    public function testThrowsOnInvalidInputWhenConstructingFromString(string $input)
    {
        static::expectException(InvalidInstant::class);

        Instant::fromString($input);
    }


    public function invalidStringInputProvider() : array
    {
        return [
            [''],
            ['2000-01-01'],
            ['12:15:00'],
            ['2000-01-01 12:15:00'],
            ['2000-01-01T12:15:00'],
            ['2000-01-01 12:15:00Z'],
        ];
    }


    /**
     * @dataProvider dateInputProvider
     */
    public function testConvertsInputToUtcTimezone(\DateTimeInterface $input) : void
    {
        $instant = new Instant($input);

        static::assertSame('UTC', $instant->toDateTime()->getTimezone()->getName());
    }


    /**
     * @dataProvider dateInputProvider
     */
    public function testConvertsInputToDateTimeImmutable(\DateTimeInterface $input) : void
    {
        $instant = new Instant($input);

        static::assertInstanceOf(\DateTimeImmutable::class, $instant->toDateTime());
    }


    public function dateInputProvider() : \Generator
    {
        foreach ([\DateTime::class, \DateTimeImmutable::class] as $class) {
            yield [new $class('now', new \DateTimeZone('Europe/London'))];
            yield [new $class('now', new \DateTimeZone('America/Chicago'))];
        }
    }


    /**
     * @dataProvider stringRepresentationProvider
     */
    public function testReturnsExpectedStringRepresentation(\DateTimeInterface $input, string $expected) : void
    {
        static::assertSame($expected, (new Instant($input))->toString());
    }


    public function stringRepresentationProvider() : \Generator
    {
        $tz = new \DateTimeZone('UTC');

        foreach ([\DateTime::class, \DateTimeImmutable::class] as $class) {
            yield [new $class('1970-01-01 00:00:00', $tz), '1970-01-01T00:00:00Z'];
            yield [new $class('1981-09-15 07:20:01', $tz), '1981-09-15T07:20:01Z'];
        }
    }


    private static function withDefaultTimezone(string $timezone, callable $fn)
    {
        $previous = date_default_timezone_get();

        date_default_timezone_set($timezone);

        try {
            return $fn();
        } finally {
            date_default_timezone_set($previous);
        }
    }
}
