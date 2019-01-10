<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Exception\DuplicateAttribute;

final class Assertion
{
    private $attributes;


    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }


    public static function fromDocument(SamlDocument $document) : self
    {
        $attributes = self::extractAttributes($document);

        return new self($attributes);
    }


    public function attributes() : array
    {
        return $this->attributes;
    }


    private static function extractAttributes(SamlDocument $document) : array
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

        $attributeQuery .= '/saml:AttributeStatement/saml:Attribute';
        $nodeList        = $document->query($attributeQuery);

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

                if ($valueNode->nodeName === 'AttributeValue' || $valueNode->nodeName === 'saml:AttributeValue') {
                    // TODO: Check the xsi:type of the value and convert to the best PHP value?
                    $values[] = self::extractAttributeValue($valueNode);
                }
            }

            $attributes[$fqn] = new Attribute($name, $values, $nameFormat, $friendlyName);
        }

        return array_values($attributes);
    }


    private static function extractIdFromReferenceNode(\DOMElement $referenceNode) : string
    {
        $uriNode = $referenceNode->attributes->getNamedItem('URI');

        if ($uriNode) {
            return substr($uriNode->nodeValue, 1);
        }

        return $referenceNode
            ->parentNode // ds:SignedInfo
            ->parentNode // ds:Signature
            ->parentNode // samlp:Response or saml:Assertion
            ->attributes
            ->getNamedItem('ID')
            ->nodeValue;
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
}