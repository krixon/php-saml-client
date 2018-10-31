<?php

namespace Krixon\SamlClient;

use Krixon\SamlClient\Protocol\RequestId;

class History implements \Serializable
{
    private $stack = [];


    public function push(RequestId $id) : void
    {
        $this->stack[] = $id;
    }


    public function serialize()
    {
        return serialize($this->stack);
    }


    public function unserialize($serialized)
    {
        $this->stack = unserialize($serialized);
    }
}
