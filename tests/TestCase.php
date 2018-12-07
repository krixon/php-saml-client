<?php

namespace Krixon\SamlClient\Test;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }


    protected function createUri(string $uri = 'http://example.com') : UriInterface
    {
        return new Uri($uri);
    }


    protected function createServerRequest(
        string $method = 'POST',
        string $uri = 'http://example.com'
    ) : ServerRequestInterface {
        return new ServerRequest($method, $uri);
    }
}
