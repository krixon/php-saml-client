<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Exception;

final class Instant
{
    private const SAML_FORMAT = 'Y-m-d\TH:i:s\Z';

    private $datetime;


    public function __construct(\DateTimeInterface $datetime)
    {
        $this->datetime = self::convertToUtcImmutable($datetime);
    }


    public static function now() : self
    {
        return new self(new \DateTimeImmutable());
    }


    public static function fromString(string $input) : self
    {
        // $input is checked explicitly before constructing the DateTimeImmutable object as we require a
        // specific format. Anything else should be rejected as invalid.

        $input = trim($input);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/', $input)) {
            throw new Exception\InvalidInstant($input);
        }

        return new self(new \DateTimeImmutable($input));
    }


    public function toString() : string
    {
        return $this->datetime->format(self::SAML_FORMAT);
    }


    public function toDateTime() : \DateTimeImmutable
    {
        return $this->datetime;
    }


    private static function convertToUtcImmutable(\DateTimeInterface $datetime) : \DateTimeImmutable
    {
        if ($datetime instanceof \DateTime) {
            // Note: The timezone is explicitly set as a second step below rather than being passed to the
            // constructor because otherwise it is ignored and an offset-based timezone is used. This
            // means if getName() is called on the timezone, it will return '+00:00' rather than 'UTC'.
            // For consistency, this approach ensures a proper UTC timezone is always used.
            $timestamp = $datetime->getTimestamp();
            $datetime  = new \DateTimeImmutable("@$timestamp");
        }

        if ($datetime->getTimezone()->getName() !== 'UTC') {
            $datetime = $datetime->setTimezone(new \DateTimeZone('UTC'));
        }

        return $datetime;
    }
}
