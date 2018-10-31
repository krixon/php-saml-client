<?php

namespace Krixon\SamlClient\Protocol;

final class RequestId
{
    private $id;


    private function __construct(string $id)
    {
        $this->id = $id;
    }


    public static function generate() : self
    {
        return new self(bin2hex(random_bytes(16)));
    }


    public function toString() : string
    {
        return $this->id;
    }
}
