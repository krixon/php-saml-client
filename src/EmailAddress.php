<?php

namespace Krixon\SamlClient;

final class EmailAddress
{
    private $address;


    public function __construct(string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("'$address' is not a valid email address.");
        }

        $this->address = $address;
    }
}
