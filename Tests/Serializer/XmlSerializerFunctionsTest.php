<?php

namespace Koded\Stdlib\Tests;

use Koded\Stdlib\Tests\Serializer\XmlSerializerTest;
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\{xml_serialize, xml_unserialize};

class XmlSerializerFunctionsTest extends TestCase
{
    public function test_serialize_to_xml()
    {
        $this->assertXmlStringEqualsXmlFile(XmlSerializerTest::XML_FILE,
            xml_serialize('payload', require XmlSerializerTest::PHP_FILE));
    }

    public function test_unserialize()
    {
        $this->assertEquals(require XmlSerializerTest::PHP_FILE,
            xml_unserialize(file_get_contents(XmlSerializerTest::XML_FILE)));
    }

    public function test_unserialize_error_should_return_empty_array()
    {
        $this->assertSame([], xml_unserialize(''));
    }
}
