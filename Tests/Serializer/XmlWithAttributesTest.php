<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Stdlib\Serializer\XmlSerializer;
use PHPUnit\Framework\TestCase;


class XmlWithAttributesTest extends TestCase
{
    public function test_with_attributes_serialize()
    {
        $serializer = new XmlSerializer('serializer');

        $xml = $serializer->serialize(require __DIR__ . '/../fixtures/serializer-fixture.php');

        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/../fixtures/serializer-fixture.xml', $xml);
    }

    public function test_xml_with_attributes_unserialize()
    {
        $serializer = new XmlSerializer(null);

        $arr = $serializer->unserialize(file_get_contents(__DIR__ . '/../fixtures/serializer-fixture.xml'));

        $this->assertSame(require __DIR__ . '/../fixtures/serializer-fixture.php', $arr);
    }
}
