<?php

namespace Krixon\SamlClient\Protocol;

class NameId
{
    private $value;
    private $format;
    private $nameQualifier;
    private $spNameQualifier;


    public function __construct(
        string $value,
        NameIdFormat $format = null,
        string $nameQualifier = null,
        string $spNameQualifier = null
    ) {
        $this->value           = $value;
        $this->format          = $format;
        $this->nameQualifier   = $nameQualifier;
        $this->spNameQualifier = $spNameQualifier;
    }


    public function value() : string
    {
        return $this->value;
    }


    public function format() : ?NameIdFormat
    {
        return $this->format;
    }


    public function nameQualifier() : ?string
    {
        return $this->nameQualifier;
    }


    public function spNameQualifier() : ?string
    {
        return $this->spNameQualifier;
    }
}