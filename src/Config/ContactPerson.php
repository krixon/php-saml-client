<?php

namespace Krixon\SamlClient\Config;

use Krixon\SamlClient\EmailAddress;

final class ContactPerson
{
    private $name;
    private $email;


    public function __construct(string $name, EmailAddress $email)
    {
        $this->name  = $name;
        $this->email = $email;
    }
}
