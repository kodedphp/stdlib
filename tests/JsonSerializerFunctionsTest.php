<?php

namespace Tests\Koded\Stdlib;

use PHPUnit\Framework\TestCase;
use Tests\Koded\Stdlib\Serializer\JsonSerializerTest;
use function Koded\Stdlib\{json_serialize, json_unserialize};

class JsonSerializerFunctionsTest extends TestCase
{
    /**
     * @dataProvider data
     */
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
        $this->assertSame('', json_unserialize(''),
                          'Returns empty string on JSON decoding fails');
    }

    /**
     * @dataProvider options
     */
    public function test_unicode_without_json_unicode_flag($data)
    {
        $serialized = json_serialize($data, JSON_UNESCAPED_UNICODE);
        $unserialized = json_unserialize($serialized, true);

        $this->assertSame(
            <<<'JSON'
            ["1",1,"2.01",2.01,"Bj\\u00f6rk Gu\\u00f0mundsd\\u00f3ttir","Bj\u00f6rk Gu\u00f0mundsd\u00f3ttir","Bj\\u00f6rk Gu\u00f0mundsd\u00f3ttir","\\//",true,false]
            JSON,
            $serialized,
            'Transforms unicode characters into unicode representation, keep the rest intact'
        );
        $this->assertEquals($data, $unserialized);
    }

   /**
     * @dataProvider options
     */
    public function test_unicode_with_default_options($data)
    {
        $serialized = json_serialize($data);
        $unserialized = json_unserialize($serialized, true);

        $this->assertSame(
            <<<'JSON'
            ["1",1,"2.01",2.01,"Bj\\u00f6rk Gu\\u00f0mundsd\\u00f3ttir","Björk Guðmundsdóttir","Bj\\u00f6rk Guðmundsdóttir","\\//",true,false]
            JSON,
            $serialized,
            'Keep the unicode characters as-is'
        );
        $this->assertEquals($data, $unserialized);
    }

    public function data()
    {
        return [
            [
                require __DIR__ . '/fixtures/config-test.php'
            ]
        ];
    }

    public function options()
    {
        return [
            [
                require __DIR__ . '/fixtures/serializer-options.php'
            ]
        ];
    }
}
