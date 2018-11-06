<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Document\Exception\InvalidDocument;
use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Protocol\Status;

final class Response
{
    private $status;


    private function __construct(Status $status)
    {
        $this->status = $status;
    }


    public static function fromDocument(SamlDocument $document) : self
    {
        self::assertValid($document);

        $status = Status::fromDocument($document);

        return new self($status);
    }


    public function status() : Status
    {
        return $this->status;
    }


    public function isSuccess() : bool
    {
        return $this->status->code()->isSuccess();
    }


    private static function assertValid(SamlDocument $document) : void
    {
        if ($document->documentElement->hasAttribute('ID') === '') {
            throw new InvalidDocument('Authn responses must have an ID.');
        }
    }
}
