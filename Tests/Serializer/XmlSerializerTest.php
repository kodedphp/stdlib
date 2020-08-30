<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Stdlib\Serializer;
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

    public function test_non_utf8_data_should_fail_to_process_everything()
    {
        $xml = $this->serializer->serialize(require __DIR__ . '/../fixtures/non-utf8-file.php');
        $doc = $this->serializer->unserialize($xml);

        $this->assertNotEmpty($xml, 'The data is serialized in a garbage XML document');
        $this->assertSame(null, $doc, 'Deserialized garbage XML results in NULL output');
    }

    public function test_xsi_nil_with_false_value_should_not_assign_null_value()
    {
        $xml = <<<'XML'
        <payload xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <id xsi:nil="false">123</id>
        </payload>
        XML;

        $data = $this->serializer->unserialize($xml);
        $doc = $this->serializer->serialize($data);

        $this->assertEquals(['id' => ['@xsi:nil' => 'false', '#' => '123']], $data, 'xsi:nil is ignored');
        $this->assertXmlStringEqualsXmlString($xml, $doc, 'xsi:nil is treated as a common attribute');
    }

    public function test_val_method()
    {
        $serializer = new XmlSerializer(null);
        $this->assertSame('#', $serializer->val(), 'Default name is "#"');

        $serializer = new XmlSerializer(null, '$val');
        $this->assertSame('$val', $serializer->val(), 'val name is now custom');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(Serializer::E_INVALID_SERIALIZER);
        new XmlSerializer('', '@');
    }

    public function test_name()
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
