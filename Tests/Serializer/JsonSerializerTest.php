<?php

namespace Koded\Stdlib\Tests\Serializer;

use ArrayIterator;
use Koded\Stdlib\Serializer;
use Koded\Stdlib\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Koded\Stdlib\{json_serialize, json_unserialize};

class JsonSerializerTest extends TestCase
{

    const SERIALIZED_JSON = '{"object":{},"array":[],"str":"php","float":2.5,"int":7,"bool":false,"null":null}';

    /** @var JsonSerializer */
    private $SUT;

    /** @dataProvider data */
    public function test_serialize($data)
    {
        $this->assertEquals(self::SERIALIZED_JSON, $this->SUT->serialize($data));
    }

    public function test_serialize_iterable()
    {
        $data = json_unserialize(self::SERIALIZED_JSON);
        $iter = new ArrayIterator($data);

        $this->assertEquals(self::SERIALIZED_JSON, $this->SUT->serialize($iter));
    }

    public function test_expects_array_if_data_is_empty_array()
    {
        $this->assertSame('[]', json_serialize([]));
    }

    public function test_expects_stdClass_if_data_is_object()
    {
        $this->assertSame('{}', json_serialize(new stdClass));
    }

    public function test_expects_trailing_zero_to_be_removed()
    {
        $this->assertEquals('[2.5]', json_serialize([2.50]));
    }

    public function test_unserialize()
    {
        $this->assertEquals(json_decode(self::SERIALIZED_JSON, false), $this->SUT->unserialize(self::SERIALIZED_JSON));
    }

    public function test_unserialize_error()
    {
        $this->assertSame('', $this->SUT->unserialize(''), 'Returns empty string on JSON unserialize error');
    }

    public function testName()
    {
        $this->assertSame(Serializer::JSON, $this->SUT->type());
    }

    public function data()
    {
        return [
            [
                require __DIR__ . '/../fixtures/config-test.php'
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->SUT = new JsonSerializer;
    }
}
