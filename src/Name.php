<?php

namespace Krixon\SamlClient;

final class Name
{
    private $name;


    public function __construct(string $name)
    {
        $this->name = trim($name);
    }


    public static function fromString(string $name) : self
    {
        return new self($name);
    }


    public function toString() : string
    {
        return $this->name;
    }
}
