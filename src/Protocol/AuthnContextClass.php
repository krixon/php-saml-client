<?php

namespace Krixon\SamlClient\Protocol;

/**
 * @see http://docs.oasis-open.org/security/saml/v2.0/saml-authn-context-2.0-os.pdf
 */
final class AuthnContextClass
{
    private const AUTHENTICATED_TELEPHONY        = 'urn:oasis:names:tc:SAML:2.0:ac:classes:AuthenticatedTelephony';
    private const INTERNET_PROTOCOL              = 'urn:oasis:names:tc:SAML:2.0:ac:classes:InternetProtocol';
    private const INTERNET_PROTOCOL_PASSWORD     = 'urn:oasis:names:tc:SAML:2.0:ac:classes:InternetProtocolPassword';
    private const KERBEROS                       = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos';
    private const MOBILE_ONE_FACTOR_UNREGISTERED = 'urn:oasis:names:tc:SAML:2.0:ac:classes:MobileOneFactorUnregistered';
    private const MOBILE_ONE_FACTOR_CONTRACT     = 'urn:oasis:names:tc:SAML:2.0:ac:classes:MobileOneFactorContract';
    private const MOBILE_TWO_FACTOR_UNREGISTERED = 'urn:oasis:names:tc:SAML:2.0:ac:classes:MobileTwoFactorUnregistered';
    private const MOBILE_TWO_FACTOR_CONTRACT     = 'urn:oasis:names:tc:SAML:2.0:ac:classes:MobileTwoFactorContract';
    private const NOMAD_TELEPHONY                = 'urn:oasis:names:tc:SAML:2.0:ac:classes:NomadTelephony';
    private const PASSWORD                       = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password';
    private const PASSWORD_PROTECTED_TRANSPORT   = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport';
    private const PERSONAL_TELEPHONY             = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PersonalTelephony';
    private const PGP                            = 'urn:oasis:names:tc:SAML:2.0:ac:classes:PGP';
    private const SECURE_REMOTE_PASSWORD         = 'urn:oasis:names:tc:SAML:2.0:ac:classes:SecureRemotePassword';
    private const SMARTCARD                      = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Smartcard';
    private const SMARTCARD_PKI                  = 'urn:oasis:names:tc:SAML:2.0:ac:classes:SmartcardPKI';
    private const SOFTWARE_PKI                   = 'urn:oasis:names:tc:SAML:2.0:ac:classes:SoftwarePKI';
    private const SPKI                           = 'urn:oasis:names:tc:SAML:2.0:ac:classes:SPKI';
    private const TELEPHONY                      = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Telephony';
    private const TIME_SYNC_TOKEN                = 'urn:oasis:names:tc:SAML:2.0:ac:classes:TimeSyncToken';
    private const TLS_CLIENT                     = 'urn:oasis:names:tc:SAML:2.0:ac:classes:TLSClient';
    private const UNSPECIFIED                    = 'urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified';
    private const X509                           = 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509';
    private const XMLDSIG                        = 'urn:oasis:names:tc:SAML:2.0:ac:classes:XMLDSig';

    private $class;


    public function __construct(string $class)
    {
        // Class is not restricted to the set of constants defined on this class as custom extensions can be used.
        $this->class = $class;
    }


    public static function authenticatedTelephony() : self
    {
        return new self(self::AUTHENTICATED_TELEPHONY);
    }


    public static function internetProtocol() : self
    {
        return new self(self::INTERNET_PROTOCOL);
    }


    public static function internetProtocolPassword() : self
    {
        return new self(self::INTERNET_PROTOCOL_PASSWORD);
    }


    public static function kerberos() : self
    {
        return new self(self::KERBEROS);
    }


    public static function mobileOneFactorUnregistered() : self
    {
        return new self(self::MOBILE_ONE_FACTOR_UNREGISTERED);
    }


    public static function mobileOneFactorContract() : self
    {
        return new self(self::MOBILE_ONE_FACTOR_CONTRACT);
    }


    public static function mobileTwoFactorUnregistered() : self
    {
        return new self(self::MOBILE_TWO_FACTOR_UNREGISTERED);
    }


    public static function mobileTwoFactorContract() : self
    {
        return new self(self::MOBILE_TWO_FACTOR_CONTRACT);
    }


    public static function nomadTelephony() : self
    {
        return new self(self::NOMAD_TELEPHONY);
    }


    public static function password() : self
    {
        return new self(self::PASSWORD);
    }


    public static function passwordProtectedTransport() : self
    {
        return new self(self::PASSWORD_PROTECTED_TRANSPORT);
    }


    public static function personalTelephony() : self
    {
        return new self(self::PERSONAL_TELEPHONY);
    }


    public static function pgp() : self
    {
        return new self(self::PGP);
    }


    public static function secureRemotePassword() : self
    {
        return new self(self::SECURE_REMOTE_PASSWORD);
    }


    public static function smartCard() : self
    {
        return new self(self::SMARTCARD);
    }


    public static function smartCardPki() : self
    {
        return new self(self::SMARTCARD_PKI);
    }


    public static function softwarePki() : self
    {
        return new self(self::SOFTWARE_PKI);
    }


    public static function spki() : self
    {
        return new self(self::SPKI);
    }


    public static function telephony() : self
    {
        return new self(self::TELEPHONY);
    }


    public static function timeSyncToken() : self
    {
        return new self(self::TIME_SYNC_TOKEN);
    }


    public static function tlsClient() : self
    {
        return new self(self::TLS_CLIENT);
    }


    public static function unspecified() : self
    {
        return new self(self::UNSPECIFIED);
    }


    public static function x509() : self
    {
        return new self(self::X509);
    }


    public static function xmlDSig() : self
    {
        return new self(self::XMLDSIG);
    }


    public function toString() : string
    {
        return $this->class;
    }


    public function equals(self $other) : bool
    {
        return $this->class === $other->class;
    }
}
