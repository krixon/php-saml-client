<?php

namespace Krixon\SamlClient\Compression;

interface Deflator
{
    public function deflate(string $data) : string;
}
