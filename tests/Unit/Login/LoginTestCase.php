<?php

namespace Krixon\SamlClient\Test\Unit\Login;

use Krixon\SamlClient\Login\RequestBuilder;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Test\Unit\TestCase;

class LoginTestCase extends TestCase
{
    protected const INSTANT = '2018-01-01T00:00:00Z';


    protected function baseBuilder() : RequestBuilder
    {
        return RequestBuilder
            ::for($this->createUri())
            ->issueInstant(Instant::fromString(self::INSTANT));
    }
}
