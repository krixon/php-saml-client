<?php

namespace Krixon\SamlClient\Protocol\Assertion\SubjectConfirmation;

final class Method
{
    // https://docs.oasis-open.org/security/saml/v2.0/saml-profiles-2.0-os.pdf §3
    private const BEARER          = 'urn:oasis:names:tc:SAML:2.0:cm:bearer';
    private const HOLDER_OF_KEY   = 'urn:oasis:names:tc:SAML:2.0:cm:holder-of-key';
    private const SENDER_VOUCHERS = 'urn:oasis:names:tc:SAML:2.0:cm:sender-vouches';

    private $method;


    private function __construct(string $method)
    {
        // URI references identifying SAML-defined confirmation methods are currently defined in the SAML profiles
        // specification. Additional methods MAY be added by defining new URIs and profiles or by private agreement.
        // For this reason, $method is not checked against the set of known methods.
        $this->method = $method;
    }


    public static function fromString(string $method) : self
    {
        return new self($method);
    }


    public function isBearer() : bool
    {
        return $this->method === self::BEARER;
    }
}