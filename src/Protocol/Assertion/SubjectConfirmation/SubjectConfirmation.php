<?php

namespace Krixon\SamlClient\Protocol\Assertion\SubjectConfirmation;

use Krixon\SamlClient\Protocol\NameId;

class SubjectConfirmation
{
    private $method;
    private $nameId;
    private $data;


    public function __construct(Method $method, NameId $nameId = null, Data $data = null)
    {
        $this->method = $method;
        $this->nameId = $nameId;
        $this->data   = $data;
    }
}