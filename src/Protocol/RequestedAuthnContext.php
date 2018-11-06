<?php

namespace Krixon\SamlClient\Protocol;

use Krixon\SamlClient\Document\Element;
use Krixon\SamlClient\Document\SamlDocument;
use Krixon\SamlClient\Document\XmlNamespace;

/**
 * Specifies the authentication context requirements of authentication statements returned in response to a request
 * or query.
 */
final class RequestedAuthnContext extends Element
{
    private const EXACT   = 'exact';
    private const MINIMUM = 'minimum';
    private const MAXIMUM = 'maximum';
    private const BETTER  = 'better';

    private $comparison;
    private $contextClasses;


    private function __construct($comparison, AuthnContextClass ...$contextClasses)
    {
        $this->comparison     = $comparison;
        $this->contextClasses = $contextClasses;
    }


    public static function exact(AuthnContextClass ...$contextClasses) : self
    {
        return new self(self::EXACT, ...$contextClasses);
    }


    public static function minimum(AuthnContextClass ...$contextClasses) : self
    {
        return new self(self::MINIMUM, ...$contextClasses);
    }


    public static function maximum(AuthnContextClass ...$contextClasses) : self
    {
        return new self(self::MAXIMUM, ...$contextClasses);
    }


    public static function better(AuthnContextClass ...$contextClasses) : self
    {
        return new self(self::BETTER, ...$contextClasses);
    }


    public function withAppendedContextClass(AuthnContextClass $contextClass) : self
    {
        if ($this->hasContextClass($contextClass)) {
            return $this;
        }

        $instance = clone $this;

        $instance->contextClasses[] = $contextClass;

        return $instance;
    }


    public function appendTo(SamlDocument $document, \DOMNode $parent) : void
    {
        $document->registerNamespace(XmlNamespace::saml());

        $element = self::appendDomElement($parent, 'samlp:RequestedAuthnContext');

        $element->setAttribute('Comparison', $this->comparison);

        $contextClasses = $this->contextClasses;

        // If nothing else is specified, use the default class.
        if (empty($contextClasses)) {
            $contextClasses[] = AuthnContextClass::passwordProtectedTransport();
        }

        foreach ($contextClasses as $contextClass) {
            $element->appendChild($document->createElement('saml:AuthnContextClassRef', $contextClass->toString()));
        }
    }


    private function hasContextClass(AuthnContextClass $contextClass) : bool
    {
        foreach ($this->contextClasses as $candidate) {
            if ($candidate->equals($contextClass)) {
                return true;
            }
        }

        return false;
    }
}
