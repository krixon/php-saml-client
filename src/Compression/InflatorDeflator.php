<?php

namespace Krixon\SamlClient\Compression;

interface InflatorDeflator
{
    public function inflate(string $data) : string;
    public function deflate(string $data) : string;
}
