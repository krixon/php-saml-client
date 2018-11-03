<?php

namespace Krixon\SamlClient;

use Psr\Http\Message\UriInterface;

final class Organisation
{
    private $language = 'en-US'; // TODO: Check name and format against spec. Is it locale?
    private $name;
    private $displayName;
    private $url;


    public function __construct(string $language, Name $name, Name $displayName, UriInterface $uri)
    {
        $this->language    = $language;
        $this->name        = $name;
        $this->displayName = $displayName;
        $this->url         = $uri;
    }


    public function displayName() : Name
    {
        return $this->displayName;
    }
}
