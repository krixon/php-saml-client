<?php

namespace Krixon\SamlClient\Test\Unit\Config;

use Krixon\SamlClient\EmailAddress;
use PHPUnit\Framework\TestCase;

class EmailAddressTest extends TestCase
{
    /**
     * @dataProvider invalidAddressProvider
     */
    public function testThrowsOnInvalidAddress(string $address) : void
    {
        static::expectException(\InvalidArgumentException::class);

        new EmailAddress($address);
    }


    public function invalidAddressProvider() : array
    {
        return [
            [''],
            ['foo'],
        ];
    }
}
