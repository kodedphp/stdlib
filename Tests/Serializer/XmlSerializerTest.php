<?php

namespace Koded\Stdlib\Serializer;

use Koded\Stdlib\Interfaces\Serializer;
use PHPUnit\Framework\TestCase;

class XmlSerializerTest extends TestCase
{

    const XML_FILE = __DIR__ . '/../fixtures/error-message.xml';
    const PHP_FILE = __DIR__ . '/../fixtures/error-message.php';

    /** @var XmlSerializer */
    private $SUT;

    public function test_serialize()
    {
        $xml = $this->SUT->serialize(require self::PHP_FILE);
        $this->assertXmlStringEqualsXmlFile(self::XML_FILE, $xml);
    }

    public function test_unserialize()
    {
        $array = $this->SUT->unserialize(file_get_contents(self::XML_FILE));
        $this->assertEquals(require self::PHP_FILE, $array);
    }

    public function test_unserialize_error_should_return_empty_array()
    {
        $this->assertSame([], $this->SUT->unserialize(''));
    }

    public function test_frankenstein_array()
    {
        $array = require __DIR__ . '/../fixtures/nested-array.php';
        $this->SUT->serialize($array);
        $this->assertEquals(require __DIR__ . '/../fixtures/nested-array.php', $array);
    }

    public function testName()
    {
        $this->assertSame(Serializer::XML, $this->SUT->name());
    }

    protected function setUp()
    {
        $this->SUT = new XmlSerializer('payload');
    }
}
