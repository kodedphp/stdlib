<?php

namespace Koded\Stdlib;

use Koded\Stdlib\Serializer\XmlSerializerTest;
use PHPUnit\Framework\TestCase;

class XmlSerializerFunctionsTest extends TestCase
{

    public function test_serialize_to_xml()
    {
        $this->assertXmlStringEqualsXmlFile(
            XmlSerializerTest::XML_FILE,
            xml_serialize('payload', require XmlSerializerTest::PHP_FILE)
        );
    }

    public function test_unserialize()
    {
        $this->markTestSkipped();

        $this->assertEquals(
            require XmlSerializerTest::PHP_FILE,
            xml_unserialize('payload', file_get_contents(XmlSerializerTest::XML_FILE))
        );
    }

    public function test_unserialize_error_should_return_empty_array()
    {
        $this->assertSame([], xml_unserialize('', ''));
    }
}
