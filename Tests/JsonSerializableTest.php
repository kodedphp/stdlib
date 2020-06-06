<?php

namespace Koded\Stdlib\Tests;

use Koded\Stdlib\{Arguments, Immutable};
use PHPUnit\Framework\TestCase;

class JsonSerializableTest extends TestCase
{

    public function test_json_serializable_interface_implementation()
    {
        $SUT = new Arguments(['foo' => new Arguments(['bar' => 'gir'])]);
        $json = json_encode($SUT);
        $this->assertSame('{"foo":{"bar":"gir"}}', $json);
    }

    public function test_json_failed_encoding()
    {
        $stdClass = new \stdClass;
        $stdClass->hello = 'there';

        $args = [
            'integer' => 123,
            'float' => 0.17,
            'string' => 'fubar',
            'bool' => true,
            'null' => null,
            'array' => ['foo' => 'bar'],
            'stdClass' => $stdClass
        ];

        $SUT = new Immutable($args);

        $json = $SUT->toJSON();
        $this->assertSame('{"integer":123,"float":0.17,"string":"fubar","bool":true,"null":null,"array":{"foo":"bar"},"stdClass":{"hello":"there"}}', $json);

        $expected = $args;
        $expected['array'] = (object)['foo' => 'bar'];
        $this->assertEquals((object)$expected, json_decode($json), 'The arrays are lost after json_decode($, false)');

        $expected['array'] = $args['array'];
        $expected['stdClass'] = (array)$args['stdClass'];

        $this->assertEquals($expected, json_decode($json, true), 'The objects are lost after json_decode($, true)');
    }
}
