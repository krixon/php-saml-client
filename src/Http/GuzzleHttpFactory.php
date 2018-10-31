<?php

namespace Krixon\SamlClient\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

use function GuzzleHttp\Psr7\stream_for;

class GuzzleHttpFactory implements HttpFactory
{
    public function createRequest(string $method, UriInterface $uri) : RequestInterface
    {
        return new Request($method, $uri);
    }


    public function createResponse() : ResponseInterface
    {
        return new Response();
    }


    public function createStream(string $content) : StreamInterface
    {
        return stream_for($content);
    }


    public function createUri(string $uri) : UriInterface
    {
        return new Uri($uri);
    }
}
