<?php

namespace Krixon\SamlClient\Compression;

class ZlibInflatorDeflator implements InflatorDeflator
{
    public function deflate(string $data) : string
    {
        return gzdeflate($data);
    }


    public function inflate(string $data) : string
    {
        return gzinflate($data);
    }
}
