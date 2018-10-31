<?php

namespace Krixon\SamlClient\Test;

use Krixon\SamlClient\Http\GuzzleHttpFactory;
use Krixon\SamlClient\Http\HttpFactory;
use Psr\Http\Message\UriInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    private $httpFactory;


    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->httpFactory = new GuzzleHttpFactory();
    }


    protected function httpFactory() : HttpFactory
    {
        return $this->httpFactory;
    }


    protected function createUri(string $uri = 'http://example.com') : UriInterface
    {
        return $this->httpFactory->createUri($uri);
    }
}
