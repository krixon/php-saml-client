<?php

namespace Krixon\SamlClient\Config\Security;

class IdentityProvider
{
    private $signedMessages = false;
    private $encryptedAssertions = false;
    private $signedAssertions = false;
    private $nameId = true;
    private $encryptedNameId = false;
    private $validXml = true;
    private $destination = true;
}
