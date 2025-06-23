<?php

namespace Tests\Koded\Stdlib;

use Error;
use Koded\Stdlib\Arguments;
use Koded\Stdlib\Config;
use Koded\Stdlib\ExtendedArguments;
use Koded\Stdlib\Tapped;
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\tap;

class TapFunctionTest extends TestCase
{
    use ObjectPropertyTrait;

    public function test_object_with_callback()
    {
        $conf = tap(new Config, function(Config $conf) {
            $conf->silent(true);
            $conf->foo = 'bar';
        });

        $this->assertInstanceOf(Config::class, $conf);
        $this->assertTrue($this->property($conf, 'silent'));
        $this->assertSame('bar', $conf->foo);
    }

    public function test_object_without_callback()
    {
        $conf = tap(new Config);
        $conf->foo = 'bar';

        $this->assertInstanceOf(Tapped::class, $conf);
        $this->assertSame('bar', $conf->foo);
    }

    public function test_extended_arguments()
    {
        $args = tap(new ExtendedArguments, function(ExtendedArguments $args) {
            $args
                ->import((array)null)
                ->set('foo', 'bar');
        });

        $this->assertSame('bar', $args->get('foo'));
    }

    public function test_with_array_value()
    {
        $arr = tap([], function(&$arr) {
            $arr['foo'] = 'bar';
        });

        $this->assertSame(['foo' => 'bar'], $arr);
    }

    public function test_array_value_without_callback()
    {
        $original = [];
        $arr = tap($original);

        $original['foo'] = 'bar';

        $this->assertSame(['foo' => 'bar'], $original);
        $this->assertInstanceOf(Tapped::class, $arr);
    }

    public function test_chainable_methods()
    {
        $data = tap(new Arguments(['foo' => 'bar']), function ($data) {
            $data
                ->set('bar', [1, 2 , 3])
                ->delete('bar.1')
                ->import([
                    100 => true,
                    101 => false,
                    102 => true
                ]);
        });

        $this->assertSame(
            [
                'foo' => 'bar',
                'bar' => [1, 2 ,3],
                100 => true,
                101 => false,
                102 => true
            ],
            $data->toArray()
        );
    }

    public function test_primitive_by_value()
    {
        $value = tap(42, function($value) { $value = 'fubar'; });
        $this->assertSame(42, $value,
            'The tapped value is not changed (passed by value)');
    }

    public function test_primitive_by_reference()
    {
        $value = tap(42, function(&$value) { $value = 'fubar'; });
        $this->assertSame('fubar', $value,
            'The tapped value is changed (passed as reference)');
    }

    public function test_unreasonable_use()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Attempt to assign property "foo" on int');

        tap(42, function($v) {
            $v->foo = 'bar';
        });
    }
}
