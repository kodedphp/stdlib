<?php

namespace Tests\Koded\Stdlib;

use PHPUnit\Framework\TestCase;
use Tests\Koded\Stdlib\Serializer\JsonSerializerTest;
use function Koded\Stdlib\json_unserialize;

class JsonSerializerFunctionsTest extends TestCase
{
    /** @dataProvider data */
    public function test_serialize_to_json($data)
    {
        $this->assertEquals(JsonSerializerTest::SERIALIZED_JSON, json_encode($data));
    }

    public function test_unserialize_json()
    {
        $this->assertEquals(
            json_decode(JsonSerializerTest::SERIALIZED_JSON, false),
            json_unserialize(JsonSerializerTest::SERIALIZED_JSON)
        );
    }

    public function test_unserialize_error()
    {
        $this->assertSame('', json_unserialize(''), 'Returns empty string on JSON decoding fails');
    }

    public function data()
    {
        return [
            [
                require __DIR__ . '/fixtures/config-test.php'
            ]
        ];
    }
}
