<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Stdlib\Serializer\XmlSerializer;
use PHPUnit\Framework\TestCase;

class XmlSerializerTest extends TestCase
{
    const XML_FILE = __DIR__ . '/../fixtures/error-message.xml';
    const PHP_FILE = __DIR__ . '/../fixtures/error-message.php';

    /** @var XmlSerializer */
    private $serializer;

    /**
     * @dataProvider singleValues
     */
    public function test_serialize_unserialize_single_value($value, $expect)
    {
        $this->assertXmlStringEqualsXmlString($expect, $this->serializer->serialize($value));
        $this->assertSame($value, $this->serializer->unserialize($expect));
    }

    public function test_unserialize_returns_simple_root_value()
    {
        $data = $this->serializer->unserialize('<foo>bar</foo>');
        $this->assertSame('bar', $data);
    }

    public function test_serialize_array()
    {
        $xml = $this->serializer->serialize(require self::PHP_FILE);
        $this->assertXmlStringEqualsXmlFile(self::XML_FILE, $xml);
    }

    public function test_unserialize()
    {
        $array = $this->serializer->unserialize(file_get_contents(self::XML_FILE));
        $this->assertEquals(require self::PHP_FILE, $array);

        // check value types
        $this->assertSame(true, $array['handled']);
        $this->assertSame('', $array['empty']);
        $this->assertNull($array['nothing']);
        $this->assertSame(42.0, $array['argument'][2]);
        $this->assertInstanceOf(\DateTimeImmutable::class, $array['datetime']);
        $this->assertInstanceOf(\stdClass::class, $array['object']);
    }

    public function test_unserialize_non_xml_values()
    {
        $this->assertNull($this->serializer->unserialize(false));
        $this->assertNull($this->serializer->unserialize([]));
        $this->assertNull($this->serializer->unserialize('123'));
    }

    public function test_non_utf8_file_should_fail_to_serialize()
    {
        $xml = $this->serializer->serialize(require __DIR__ . '/../fixtures/non-utf8-file.php');
        $this->assertSame('', $xml);
    }

    public function testName()
    {
        $this->assertSame('xml', $this->serializer->type());
    }

    /*
     * data provider for single values
     */
    public function singleValues()
    {
        return [
            ['', '<payload></payload>'],
            ['', '<payload><!-- comments are ignored --></payload>'],
            [null, '<payload xsi:nil="true"/>'],
            ['bar', '<payload>bar</payload>'],
            ['123', '<payload>123</payload>'],
            [123, '<payload type="xsd:integer">123</payload>'],
            [[], '<payload xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>'],
            [[123], '<payload xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<item key="0" type="xsd:integer">123</item></payload>'
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->serializer = new XmlSerializer('payload');
    }
}
