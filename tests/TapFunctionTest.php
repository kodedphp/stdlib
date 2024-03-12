<?php

namespace Tests\Koded\Stdlib;

use Error;
use Koded\Stdlib\Arguments;
use Koded\Stdlib\Config;
use Koded\Stdlib\Tapped;
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\tap;

class TapFunctionTest extends TestCase
{
    use ObjectPropertyTrait;

    public function test_object_with_callback()
    {
        $conf = tap(new Config, function(Config $c) {
            $c->silent(true);
            $c->foo = 'bar';
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

    public function test_with_array_value()
    {
        $arr = tap([], function(&$data) {
            $data['foo'] = 'bar';
        });

        $this->assertSame(['foo' => 'bar'], $arr);
    }

    public function test_chainable_methods()
    {
        $data = tap(new Arguments(['foo' => 'bar']), function ($args) {
            $args
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

    public function test_with_primitive_value()
    {
        $val1 = tap(42, function($v) {
            $v = 'fubar';
        });
        $this->assertSame(42, $val1,
            'The tapped value is not changed');

        $val2 = tap(42, function(&$v) {
            $v = 'fubar';
        });
        $this->assertSame('fubar', $val2,
            'The tapped value is changed (passed by reference)');
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
