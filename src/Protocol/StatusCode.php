<?php

namespace Krixon\SamlClient\Protocol;

final class StatusCode
{
    public const AUTHN_FAILED               = 'urn:oasis:names:tc:SAML:2.0:status:AuthnFailed';
    public const INVALID_ATTR_NAME_VALUE    = 'urn:oasis:names:tc:SAML:2.0:status:InvalidAttrNameOrValue';
    public const INVALID_NAME_ID_POLICY     = 'urn:oasis:names:tc:SAML:2.0:status:InvalidNameIDPolicy';
    public const NO_AUTHN_CONTEXT           = 'urn:oasis:names:tc:SAML:2.0:status:NoAuthnContext';
    public const NO_AVAILABLE_IDP           = 'urn:oasis:names:tc:SAML:2.0:status:NoAvailableIDP';
    public const NO_PASSIVE                 = 'urn:oasis:names:tc:SAML:2.0:status:NoPassive';
    public const NO_SUPPORTED_IDP           = 'urn:oasis:names:tc:SAML:2.0:status:NoSupportedIDP';
    public const PARTIAL_LOGOUT             = 'urn:oasis:names:tc:SAML:2.0:status:PartialLogout';
    public const PROXY_COUNT_EXCEEDED       = 'urn:oasis:names:tc:SAML:2.0:status:ProxyCountExceeded';
    public const REQUEST_DENIED             = 'urn:oasis:names:tc:SAML:2.0:status:RequestDenied';
    public const REQUEST_UNSUPPORTED        = 'urn:oasis:names:tc:SAML:2.0:status:RequestUnsupported';
    public const REQUEST_VERSION_DEPRECATED = 'urn:oasis:names:tc:SAML:2.0:status:RequestVersionDeprecated';
    public const REQUEST_VERSION_TOO_HIGH   = 'urn:oasis:names:tc:SAML:2.0:status:RequestVersionTooHigh';
    public const REQUEST_VERSION_TOO_LOW    = 'urn:oasis:names:tc:SAML:2.0:status:RequestVersionTooLow';
    public const REQUESTER                  = 'urn:oasis:names:tc:SAML:2.0:status:Requester';
    public const RESOURCE_NOT_RECOGNIZED    = 'urn:oasis:names:tc:SAML:2.0:status:ResourceNotRecognized';
    public const RESPONDER                  = 'urn:oasis:names:tc:SAML:2.0:status:Responder';
    public const SUCCESS                    = 'urn:oasis:names:tc:SAML:2.0:status:Success';
    public const TOO_MANY_RESPONSES         = 'urn:oasis:names:tc:SAML:2.0:status:TooManyResponses';
    public const UNKNOWN_ATTR_PROFILE       = 'urn:oasis:names:tc:SAML:2.0:status:UnknownAttrProfile';
    public const UNKNOWN_PRINCIPLE          = 'urn:oasis:names:tc:SAML:2.0:status:UnknownPrincipal';
    public const UNSUPPORTED_BINDING        = 'urn:oasis:names:tc:SAML:2.0:status:UnsupportedBinding';
    public const VERSION_MISMATCH           = 'urn:oasis:names:tc:SAML:2.0:status:VersionMismatch';

    private $code;


    private function __construct(string $code)
    {
        // Custom codes are allowed, so don't validate against the defined constants.
        $this->code = $code;
    }


    public static function fromString(string $code) : self
    {
        return new self($code);
    }


    public function isSuccess() : bool
    {
        return $this->code === self::SUCCESS;
    }
}
