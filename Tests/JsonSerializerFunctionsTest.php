<?php

namespace Koded\Stdlib;

use Koded\Exceptions\KodedException;
use Koded\Stdlib\Serializer\JsonSerializerTest;
use PHPUnit\Framework\TestCase;

class JsonSerializerFunctionsTest extends TestCase
{

    /** @dataProvider data */
    public function test_serialize_to_json($data)
    {
        $this->assertEquals(JsonSerializerTest::SERIALIZED_JSON, json_serialize($data));
    }

    public function test_unserialize_json()
    {
        $this->assertEquals(
            json_decode(JsonSerializerTest::SERIALIZED_JSON, true),
            json_unserialize(JsonSerializerTest::SERIALIZED_JSON)
        );
    }

    public function test_unserialize_error()
    {
        $this->expectException(KodedException::class);
        $this->expectExceptionMessage('[Exception] Syntax error');

        json_unserialize('');
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
