<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Document\Element;
use Krixon\SamlClient\Document\SamlDocument;

/**
 * Specifies constraints on the name identifier to be used to represent the requested subject.
 *
 * If omitted, then any type of identifier supported by the identity provider for the requested subject can be used,
 * constrained by any relevant deployment-specific policies, with respect to privacy, for example.
 */
final class NameIdPolicy extends Element
{
    private $format;
    private $nameQualifier;
    private $allowCreate;


    /**
     * @param string|null $nameQualifier Specifies that the assertion subject's identifier be returned (or created)
     *                                   in the namespace of a service provider other than the requester, or in the
     *                                   namespace of an affiliation group of service providers.
     * @param NameFormat  $format        Specifies the desired name identifier format.
     * @param bool        $allowCreate   indicates whether the requester grants to the identity provider, in the course
     *                                   of fulfilling the request, permission to create a new identifier.
     */
    public function __construct(
        string $nameQualifier = null,
        NameFormat $format = null,
        bool $allowCreate = false
    ) {
        if (trim($nameQualifier) === '') {
            $nameQualifier = null;
        }

        if (null === $format) {
            $format = NameFormat::unspecified();
        }

        // The use of the AllowCreate attribute MUST NOT be used and SHOULD be ignored in conjunction with requests
        // for or assertions issued with name identifiers with a Format of
        // urn:oasis:names:tc:SAML:2.0:nameid-format:transient (they preclude any such state in and of themselves).
        if ($format->equals(NameFormat::transient())) {
            $allowCreate = false;
        }

        $this->format        = $format;
        $this->nameQualifier = $nameQualifier;
        $this->allowCreate   = $allowCreate;
    }


    public function appendTo(SamlDocument $document, \DOMNode $parent) : void
    {
        // Don't add an element at all if all attributes have default values.
        if ($this->isDefault()) {
            return;
        }

        $element = self::appendDomElement($parent, 'samlp:NameIDPolicy');

        if (!$this->isAllowCreateDefault()) {
            $element->setAttribute('AllowCreate', $this->allowCreate ? 'true' : 'false');
        }

        if (!$this->isFormatDefault()) {
            $element->setAttribute('Format', $this->format->toString());
        }

        if (!$this->isNameQualifierDefault()) {
            $element->setAttribute('SPNameQualifier', $this->nameQualifier);
        }
    }


    private function isFormatDefault() : bool
    {
        return $this->format->equals(NameFormat::unspecified());
    }


    private function isNameQualifierDefault() : bool
    {
        return $this->nameQualifier === null;
    }


    private function isAllowCreateDefault() : bool
    {
        return !$this->allowCreate;
    }


    private function isDefault() : bool
    {
        return $this->isFormatDefault()
            && $this->isNameQualifierDefault()
            && $this->isAllowCreateDefault();
    }
}
