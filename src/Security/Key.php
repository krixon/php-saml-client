<?php

namespace Krixon\SamlClient\Security;

final class Key
{
    private $key;
    private $passphrase;


    private function __construct(string $key, string $passphrase = null)
    {
        if (strpos($key, 'file://') === 0) {
            $key = $this->readFile($key);
        }

        $this->key        = $key;
        $this->passphrase = $passphrase;
    }


    public function toString() : string
    {
        return $this->key;
    }


    public function passphrase() : string
    {
        return $this->passphrase;
    }


    private function readFile(string $key)
    {
        $file = substr($key, 7);

        if (!is_readable($file)) {
            throw new \InvalidArgumentException("Key file '$file' cannot be read.");
        }

        return file_get_contents($file);
    }
}