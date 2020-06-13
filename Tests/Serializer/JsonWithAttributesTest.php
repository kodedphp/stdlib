<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Stdlib\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;


class JsonWithAttributesTest extends TestCase
{
    private const JSON_FILE = __DIR__ . '/../fixtures/serializer-fixture.json';
    private const PHP_FILE  = __DIR__ . '/../fixtures/serializer-fixture.php';

    public function test_with_attributes_serialize()
    {
        $json = (new JsonSerializer)->serialize(require self::PHP_FILE);

        $this->assertJson($json, 'JSON file ' . self::JSON_FILE);
        $this->assertJsonStringEqualsJsonFile(self::JSON_FILE, $json);
    }

    public function test_xml_with_attributes_unserialize()
    {
        $array = (new JsonSerializer(0, true))->unserialize(file_get_contents(self::JSON_FILE));
        $this->assertSame($array, require self::PHP_FILE);
    }
}
