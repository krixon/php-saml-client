<?php

namespace Krixon\SamlClient\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

interface HttpFactory
{
    public function createRequest(string $method, UriInterface $uri) : RequestInterface;
    public function createResponse() : ResponseInterface;
    public function createStream(string $content) : StreamInterface;
    public function createUri(string $uri) : UriInterface;
}
