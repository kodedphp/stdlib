<?php

namespace Koded\Stdlib\Serializer;

use Koded\Exceptions\KodedException;
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\{json_serialize, json_unserialize};

class JsonSerializerTest extends TestCase
{

    const SERIALIZED_JSON = '{"php-key-1":{},"normalizer":"php","timeout":2.5}';

    /** @var JsonSerializer */
    private $SUT;

    /** @dataProvider data */
    public function test_serialize($data)
    {
        $this->assertEquals(self::SERIALIZED_JSON, $this->SUT->serialize($data));
    }

    public function test_serialize_with_iterable()
    {
        $data = json_unserialize(self::SERIALIZED_JSON);
        $iter = new \ArrayIterator($data);

        $this->assertEquals(self::SERIALIZED_JSON, $this->SUT->serialize($iter));
    }

    public function test_expects_empty_object_if_data_is_empty_array()
    {
        $this->assertSame('{}', json_serialize([]));
    }

    public function test_unserialize()
    {
        $this->assertEquals(json_decode(self::SERIALIZED_JSON, true), $this->SUT->unserialize(self::SERIALIZED_JSON));
    }

    public function test_unserialize_error()
    {
        $this->expectException(KodedException::class);
        $this->expectExceptionMessage('[Exception] Syntax error');

        $this->SUT->unserialize('');
    }

    public function data()
    {
        return [
            [
                require __DIR__ . '/../fixtures/config-test.php'
            ]
        ];
    }

    protected function setUp()
    {
        $this->SUT = new JsonSerializer;
    }
}
