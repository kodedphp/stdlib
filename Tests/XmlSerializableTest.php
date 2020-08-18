<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\Immutable;
use PHPUnit\Framework\TestCase;

class XmlSerializableTest extends TestCase
{
    public function test_object_to_xml_representation()
    {
        $SUT = new Immutable(require __DIR__ .'/fixtures/error-message.php');
        $xml = $SUT->toXML('payload');

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/fixtures/error-message.xml', $xml);
    }
}
