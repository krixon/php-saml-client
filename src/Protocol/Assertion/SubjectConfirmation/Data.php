<?php

namespace Krixon\SamlClient\Protocol\Assertion\SubjectConfirmation;

use Krixon\SamlClient\Protocol\Instant;

class Data
{
    private $notBefore;
    private $notOnOrAfter;
    private $recipient;
    private $inResponseTo;


    public function __construct(
        Instant $notBefore = null,
        Instant $notOnOrAfter = null,
        string $recipient = null,
        string $inResponseTo = null
    ) {
        $this->notBefore    = $notBefore;
        $this->notOnOrAfter = $notOnOrAfter;
        $this->recipient    = $recipient;
        $this->inResponseTo = $inResponseTo;
    }


    /**
     * A time instant before which the subject cannot be confirmed.
     *
     * @return Instant|null
     */
    public function notBefore() : ?Instant
    {
        return $this->notBefore;
    }


    /**
     * A time instant at which the subject can no longer be confirmed.
     *
     * @return Instant|null
     */
    public function notOnOrAfter() : ?Instant
    {
        return $this->notOnOrAfter;
    }


    /**
     * A URI specifying the entity or location to which an attesting entity can present the assertion.
     *
     * For example, this value might indicate that the assertion must be delivered to a particular network
     * endpoint in order to prevent an intermediary from redirecting it someplace else.
     *
     * @return string|null
     */
    public function recipient() : ?string
    {
        return $this->recipient;
    }


    /**
     * The ID of a SAML protocol message in response to which an attesting entity can present the assertion.
     *
     * For example, this attribute might be used to correlate the assertion to a SAML request that resulted in its
     * presentation.
     *
     * @return string|null
     */
    public function inResponseTo() : ?string
    {
        return $this->inResponseTo;
    }
}