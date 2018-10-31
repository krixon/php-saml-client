<?php

namespace Krixon\SamlClient\Test\Unit;

use Krixon\SamlClient\Document\Element;
use Krixon\SamlClient\Document\SamlDocument;

abstract class TestCase extends \Krixon\SamlClient\Test\TestCase
{
    /**
     * @param string|\DOMDocument $expected
     */
    public static function assertElementProducesExpectedXml($expected, Element $element)
    {
        $document = SamlDocument::create('root');
        $root     = $document->firstChild;

        $element->appendTo($document, $root);

        if (null === $root->lastChild) {
            // No new element was appended.
            static::assertSame($expected, '');
            return;
        }

        $actual = $document->saveXML($root->lastChild);

        if ('' === $expected) {
            static::assertSame($expected, $actual);
        } else {
            static::assertXmlStringEqualsXmlString($expected, $actual);
        }
    }
}
