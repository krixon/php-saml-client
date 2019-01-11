<?php

namespace Krixon\SamlClient\Protocol\Assertion;

use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Exception\DuplicateAttribute;
use Krixon\SamlClient\Protocol\Assertion\SubjectConfirmation\Data;
use Krixon\SamlClient\Protocol\Assertion\SubjectConfirmation\Method;
use Krixon\SamlClient\Protocol\Assertion\SubjectConfirmation\SubjectConfirmation;
use Krixon\SamlClient\Protocol\Attribute;
use Krixon\SamlClient\Protocol\AttributeNameFormat;
use Krixon\SamlClient\Protocol\Instant;
use Krixon\SamlClient\Protocol\NameId;
use Krixon\SamlClient\Protocol\NameIdFormat;

final class Assertion
{
    private $attributes;
    private $id;
    private $nameId;
    private $subjectConfirmations;


    /**
     * @param string                $id
     * @param Attribute[]           $attributes
     * @param NameId|null           $nameId
     * @param SubjectConfirmation[] $subjectConfirmations
     */
    public function __construct(
        string $id,
        array $attributes,
        NameId $nameId = null,
        array $subjectConfirmations = []
    ) {
        $this->id                   = $id;
        $this->attributes           = $attributes;
        $this->nameId               = $nameId;
        $this->subjectConfirmations = $subjectConfirmations;
    }


    public static function fromDocument(SamlDocument $document) : self
    {
        $id                   = self::extractId($document);
        $attributes           = self::extractAttributes($document);
        $nameId               = self::extractSubjectNameId($document);
        $subjectConfirmations = self::extractSubjectConfirmations($document);

        return new self($id, $attributes, $nameId, $subjectConfirmations);
    }


    public function id() : string
    {
        return $this->id;
    }


    public function attributes() : array
    {
        return $this->attributes;
    }


    public function attribute(string $name) : ?Attribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->isNamed($name)) {
                return $attribute;
            }
        }

        return null;
    }


    public function firstAttributeValue(string $name, $default = null)
    {
        $attribute = $this->attribute($name);

        return $attribute ? $attribute->firstValue() : $default;
    }


    public function nameIdentifierAttribute() : ?string
    {
        return $this->firstAttributeValue('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/nameidentifier');
    }


    public function emailAttribute() : ?string
    {
        // TODO: Could also check:
        //  http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn
        //  http://schemas.xmlsoap.org/claims/EmailAddress
        //  See: https://docs.microsoft.com/en-us/windows-server/identity/ad-fs/technical-reference/the-role-of-claims
        return $this->firstAttributeValue('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress');
    }


    public function nameAttribute() : ?string
    {
        // TODO: Could also check http://schemas.xmlsoap.org/claims/CommonName?
        //       https://docs.microsoft.com/en-us/windows-server/identity/ad-fs/technical-reference/the-role-of-claims
        return $this->firstAttributeValue('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name');
    }


    public function givenNameAttribute() : ?string
    {
        return $this->firstAttributeValue('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname');
    }


    public function surnameAttribute() : ?string
    {
        return $this->firstAttributeValue('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname');
    }


    public function nameId() : ?NameId
    {
        return $this->nameId;
    }


    private static function extractId(SamlDocument $document) : string
    {
        // Query for the assertion nodes.
        $nodeList = self::query($document);

        // Only the first assertion is considered. The spec technically allows multiple assertions which could be
        // supported in future.
        if ($nodeList->length > 0 && $nodeList->item(0)->hasAttribute('ID')) {
            return $nodeList->item(0)->getAttribute('ID');
        }

        return null;
    }


    private static function extractAttributes(SamlDocument $document) : array
    {
        $nodeList   = self::query($document, '/saml:AttributeStatement/saml:Attribute');
        $attributes = [];

        /** @var \DOMNode $attributeNode */
        foreach ($nodeList as $attributeNode) {
            // TODO: Support FriendlyName too.

            $nameNode = $attributeNode->attributes->getNamedItem('Name');

            if (null === $nameNode) {
                continue;
            }

            $friendlyNameNode = $attributeNode->attributes->getNamedItem('FriendlyName');
            $nameFormatNode   = $attributeNode->attributes->getNamedItem('NameFormat');
            $name             = $nameNode->nodeValue;
            $friendlyName     = $friendlyNameNode ? $friendlyNameNode->nodeValue : null;
            $nameFormat       = $nameFormatNode ? AttributeNameFormat::fromString($nameFormatNode->nodeValue) : null;

            // Attributes are identified/named by the combination of the NameFormat and Name XML
            // attributes. Neither one in isolation can be assumed to be unique, but taken together, they ought to
            // be unambiguous within a given deployment.
            // For this reason, we should not encounter multiple attributes with the same combination.

            $fqn = $nameFormat->toString() . '|' . $name;

            if (array_key_exists($fqn, $attributes)) {
                throw new DuplicateAttribute($name, $nameFormat->toString());
            }

            $values = [];

            /** @var \DOMNode $valueNode */
            foreach ($attributeNode->childNodes as $valueNode) {
                if (!$valueNode instanceof \DOMElement) {
                    continue;
                }

                if ($valueNode->localName === 'AttributeValue') {
                    $values[] = self::extractAttributeValue($valueNode);
                }
            }

            $attributes[$fqn] = new Attribute($name, $values, $nameFormat, $friendlyName);
        }

        return array_values($attributes);
    }


    private static function extractAttributeValue(\DomElement $node)
    {
        $type  = $node->attributes->getNamedItem('type');
        $value = $node->nodeValue;

        if (!$type) {
            return $value;
        }

        switch ($type->nodeValue) {
            case 'xs:int':
            case 'xs:unsignedInt':
            case 'xs:integer':
            case 'xs:positiveInteger':
            case 'xs:nonPositiveInteger':
            case 'xs:negativeInteger':
            case 'xs:nonNegativeInteger':
            case 'xs:long':
            case 'xs:unsignedLong':
            case 'xs:short':
            case 'xs:unsignedShort':
            case 'xs:byte':
            case 'xs:unsignedByte':
                if (is_numeric($value)) {
                    $value = (int)$value;
                }
                break;
            case 'xs:decimal':
            case 'xs:float':
            case 'xs:double':
                if (is_numeric($value)) {
                    $value = (float)$value;
                }
                break;
            case 'xs:boolean':
                $value = $value === 'true' || $value === '1';
                break;
            case 'xs:dateTime':
                try {
                    $value = new \DateTimeImmutable($value);
                } catch (\Exception $e) {
                    // Squash. Return the original string value instead.
                }
        }

        return $value;
    }


    private static function extractIdFromReferenceNode(\DOMElement $referenceNode) : string
    {
        $uriNode = $referenceNode->attributes->getNamedItem('URI');

        if ($uriNode) {
            return substr($uriNode->nodeValue, 1);
        }

        return $referenceNode
            ->parentNode// ds:SignedInfo
            ->parentNode// ds:Signature
            ->parentNode// samlp:Response or saml:Assertion
            ->attributes
            ->getNamedItem('ID')
            ->nodeValue;
    }


    private static function extractSubjectNameId(SamlDocument $document) : ?NameId
    {
        // TODO: (in reference to the NameIDPolicy element):
        //      When a Format defined in Section 8.3 other than urn:oasis:names:tc:SAML:1.1:nameidformat:unspecified
        //      or urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted is used,
        //      then if the identity provider returns any assertions:
        //        • the Format value of the <NameID> within the <Subject> of any <Assertion> MUST be identical to the
        //          Format value supplied in the <NameIDPolicy>, and
        //        • if SPNameQualifier is not omitted in <NameIDPolicy>, the SPNameQualifier value of the
        //          <NameID> within the <Subject> of any <Assertion> MUST be identical to the
        //          SPNameQualifier value supplied in the <NameIDPolicy>.
        //      Perhaps best enforced by the response itself as part of validating the deserialised data?

        $nodeList = self::query($document, '/saml:Subject/saml:NameID');
        $nameId   = $nodeList->length > 0 ? $nodeList->item(0) : null;

        // It's valid for a Subject and/or NameId to be omitted.
        // TODO: Spec also allows for a <BaseID> element as well as <NameID>.

        if (!$nameId instanceof \DOMElement) {
            return null;
        }

        return self::extractNameId($nameId);
    }


    private static function extractNameId(\DOMElement $node) : ?NameId
    {
        if (empty($node->nodeValue)) {
            return null;
        }

        $nameIdFormat = null;
        if ($node->hasAttribute('Format')) {
            $nameIdFormat = NameIdFormat::fromString($node->getAttribute('Format'));
        }

        $nameQualifier   = $node->hasAttribute('NameQualifier') ? $node->getAttribute('NameQualifier') : null;
        $spNameQualifier = $node->hasAttribute('SPNameQualifier') ? $node->getAttribute('SPNameQualifier') : null;

        return new NameId($node->nodeValue, $nameIdFormat, $nameQualifier, $spNameQualifier);
    }


    /**
     * @return SubjectConfirmation[]
     */
    private static function extractSubjectConfirmations(SamlDocument $document) : array
    {
        $nodeList = self::query($document, '/saml:Subject/saml:SubjectConfirmation');
        $results  = [];

        /** @var \DOMElement $node */
        foreach ($nodeList as $node) {
            $method = $node->hasAttribute('Method') ? Method::fromString($node->getAttribute('Method')) : null;
            $data   = self::extractSubjectConfirmationData($node);
            $nameId = null;

            $nameIdNodes = $node->getElementsByTagName('NameID'); // TODO: BaseID
            if ($nameIdNodes->length > 0) {
                $nameIdNode = $nameIdNodes->item(0);
                $nameId     = self::extractNameId($nameIdNode);
            }

            $results[] = new SubjectConfirmation($method, $nameId, $data);
        }

        return $results;
    }


    private static function extractSubjectConfirmationData(\DOMElement $subjectConfirmationNode) : ?Data
    {
        $nodeList = $subjectConfirmationNode->getElementsByTagName('SubjectConfirmationData');

        if ($nodeList->length === 0) {
            return null;
        }

        $node         = $nodeList->item(0);
        $inResponseTo = $node->hasAttribute('InResponseTo') ? $node->getAttribute('InResponseTo') : null;
        $recipient    = $node->hasAttribute('Recipient') ? $node->getAttribute('Recipient') : null;
        $notOnOrAfter = $node->hasAttribute('NotOnOrAfter') ? Instant::fromString($node->getAttribute('NotOnOrAfter')) : null;
        $notBefore    = $node->hasAttribute('NotBefore') ? Instant::fromString($node->getAttribute('NotBefore')) : null;

        if (!$inResponseTo || !$recipient || !$notOnOrAfter || !$notBefore) {
            return null;
        }

        return new Data($notBefore, $notOnOrAfter, $recipient, $inResponseTo);
    }


    /**
     * Runs a query for assertions, or child nodes of assertions.
     *
     * @param SamlDocument $document The document to query.
     * @param string|null  $xpath    The xpath to append to the assertion query. Used to query for assertion child
     *                               nodes.
     *
     * @return \DOMNodeList
     */
    private static function query(SamlDocument $document, string $xpath = null) : \DOMNodeList
    {
        $assertionQuery = '/samlp:Response/saml:Assertion';
        $referenceQuery = $assertionQuery . '/ds:Signature/ds:SignedInfo/ds:Reference';
        $referenceNode  = $document->query($referenceQuery)->item(0);

        if (!$referenceNode) {
            // The element was not signed. Was the document itself signed?
            $referenceQuery = '/samlp:Response/ds:Signature/ds:SignedInfo/ds:Reference';
            $referenceNode  = $document->query($referenceQuery)->item(0);
            $attributeQuery = "/samlp:Response/saml:Assertion";

            if ($referenceNode) {
                $id             = self::extractIdFromReferenceNode($referenceNode);
                $attributeQuery = "/samlp:Response[@ID='$id']/saml:Assertion";
            }
        } else {
            $id             = self::extractIdFromReferenceNode($referenceNode);
            $attributeQuery = $assertionQuery . "[@ID='$id']";
        }

        $attributeQuery .= $xpath;

        return $document->query($attributeQuery);
    }
}