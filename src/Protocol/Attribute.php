<?php

namespace Krixon\SamlClient\Protocol;

class Attribute
{
    private $name;
    private $nameFormat;
    private $friendlyName;
    private $values;


    public function __construct(
        string $name,
        array $values,
        AttributeNameFormat $nameFormat = null,
        string $friendlyName = null
    ) {
        $this->name         = $name;
        $this->nameFormat   = $nameFormat ?: AttributeNameFormat::unspecified();
        $this->friendlyName = $friendlyName;
        $this->values       = $values;
    }


    public function name() : string
    {
        return $this->name;
    }


    public function nameFormat() : AttributeNameFormat
    {
        return $this->nameFormat;
    }


    public function friendlyName() : string
    {
        return $this->friendlyName;
    }


    public function isNamed(string $name) : bool
    {
        return $this->name === $name || $this->friendlyName === $name;
    }


    public function firstValue()
    {
        return $this->values[0] ?? null;
    }


    public function values() : array
    {
        return $this->values;
    }
}