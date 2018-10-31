<?php

namespace Krixon\SamlClient\Compression;

interface Inflator
{
    public function inflate(string $data) : string;
}
