<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Document\Exception\InvalidDocument;
use Krixon\SamlClient\Document\SamlDocument;

final class Status
{
    private $code;
    private $message;
    private $detail;


    private function __construct(StatusCode $code, string $message = null, string $detail = null)
    {
        $this->code    = $code;
        $this->message = $message;
        $this->detail  = $detail;
    }


    public static function fromDocument(SamlDocument $document) : self
    {
        $nodeList = $document->query('/samlp:Response/samlp:Status');
        if ($nodeList->length !== 1) {
            throw InvalidDocument::requiredElementNotPresent('Status');
        }

        $statusNode = $nodeList->item(0);

        $nodeList = $document->query('./samlp:StatusCode', $statusNode);
        if ($nodeList->length !== 1) {
            throw InvalidDocument::requiredElementNotPresent('StatusCode');
        }

        $code = $nodeList->item(0)->getAttribute('Value');

        // TODO: Handle sub-codes. These are nested StatusCode elements beneath the main StatusCode.

        $message  = null;
        $nodeList = $document->query('./samlp:StatusMessage', $statusNode);
        if ($nodeList->length === 1) {
            $message = $nodeList->item(0)->textContent;
        }

        $detail   = null;
        $nodeList = $document->query('./samlp:StatusDetail', $statusNode);
        if ($nodeList->length === 1) {
            $detail = $nodeList->item(0)->textContent;
        }

        return new self(StatusCode::fromString($code), $message, $detail);
    }


    public function code() : StatusCode
    {
        return $this->code;
    }


    public function message() : ?string
    {
        return $this->message;
    }


    public function detail() : ?string
    {
        return $this->detail;
    }
}
