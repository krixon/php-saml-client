<?php

namespace Krixon\SamlClient\Login;

use Krixon\SamlClient\Document\Exception\DecryptionFailed;
use Krixon\SamlClient\Document\Exception\InvalidDocument;
use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Login\Exception\InvalidResponse;
use Krixon\SamlClient\Protocol\Assertion\Assertion;
use Krixon\SamlClient\Protocol\Attribute;
use Krixon\SamlClient\Protocol\Status;
use Krixon\SamlClient\Protocol\StatusCode;
use Krixon\SamlClient\Security\Key;

final class Response
{
    private $status;
    private $assertion;


    private function __construct(Status $status, Assertion $assertion = null)
    {
        $this->status    = $status;
        $this->assertion = $assertion;

        $this->assertValid();
    }


    public static function fromDocument(SamlDocument $document, Key $decryptionKey = null) : self
    {
        self::assertValidDocument($document);

        if ($document->hasEncryptedAssertion()) { // FIXME: Not a good enough check because other things can be encrypted. Look for an XMLEnc element instead
            if (!$decryptionKey) {
                throw new InvalidResponse('Response contains encrypted assertion but no decryption key was provided.');
            }
            try {
                $document = $document->decrypt($decryptionKey);
            } catch (DecryptionFailed $e) {
                throw new InvalidResponse('Response could not be decrypted.', $e);
            }
        }

        $status = Status::fromDocument($document);

        // TODO: Multiple assertions are technically valid according to the spec.
        //       One use case for multiple Assertion could be a claim about the Subject that is time limited
        //       differently from the Attribute list. e.g. perhaps some fact about the user that should only be
        //       relied on for a short period of time, defined by Conditions->NotBefore->NotOnOrAfter. Perhaps some
        //       authentication mechanism that only allows users to be authenticated at the IdP for a short period
        //       of time, compared to the longer period of time their Attribute list can be used for.
        //       However in practice, there are not many implementations which would produce or consume multiple
        //       assertions, so for simplicity only a single assertion is supported for now.
        $assertion = $document->hasAssertion() ? Assertion::fromDocument($document) : null;

        return new self($status, $assertion);
    }


    public function status() : Status
    {
        return $this->status;
    }


    public function isSuccess() : bool
    {
        return $this->status->code()->isSuccess();
    }


    /**
     * @return Attribute[]
     */
    public function attributes() : array
    {
        if (!$this->assertion) {
            return [];
        }

        return $this->assertion->attributes();
    }


    public function assertion() : ?Assertion
    {
        return $this->assertion;
    }


    /**
     * Asserts that the SAML document itself is valid in terms of structure and content.
     *
     * @param SamlDocument $document
     */
    private static function assertValidDocument(SamlDocument $document) : void
    {
        $version = $document->documentElement->getAttribute('Version');
        if ($version !== '2.0') {
            throw InvalidResponse::invalidDocument(InvalidDocument::unsupportedSamlVersion($version));
        }

        if ($document->documentElement->hasAttribute('ID') === '') {
            throw InvalidResponse::invalidDocument(new InvalidDocument('Authn responses must have an ID.'));
        }
    }


    /**
     * Asserts that the response itself is valid.
     */
    private function assertValid()
    {
        // Must have a success status code.

        if (!$this->status->isSuccess()) {
            throw new InvalidResponse(sprintf(
                "Status code expected to be '%s' but actually '%s'.",
                StatusCode::SUCCESS,
                $this->status->code()->toString()
            ));
        }
    }
}
