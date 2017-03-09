<?php

namespace Koded\Stdlib;

use Koded\Stdlib\Interfaces\{ Argument, Data };
use PHPUnit\Framework\TestCase;

class ArgumentObjectTest extends TestCase
{

    /**
     * @var Argument
     */
    private $SUT;

    public function testSet()
    {
        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ], $this->SUT->toArray());

        $result = $this->SUT->set('foo', 'gir');

        $this->assertEquals([
            'foo' => 'gir',
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ], $this->SUT->toArray());

        $this->assertInstanceOf(Arguments::class, $result);
    }

    /**
     * @depends testSet
     */
    public function testImport()
    {
        $result = $this->SUT->import(['foo' => 42, 'qux' => 2]);

        $this->assertEquals([
            'foo' => 42,
            'bar' => 2,
            'baz' => 3,
            'qux' => 2
        ], $this->SUT->toArray());
        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function testUpsert()
    {
        // not added because 'foo' already exist
        $result = $this->SUT->upsert('foo', 42);

        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ], $this->SUT->toArray());

        // added because it does not exist
        $this->SUT->upsert('poo', 42);

        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
            'poo' => 42
        ], $this->SUT->toArray());

        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function testBind()
    {
        $var = 42;
        $result = $this->SUT->bind('foo', $var);
        $this->assertEquals([
            'foo' => 42,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], $this->SUT->toArray());

        $var = 0;
        $this->assertEquals([
            'foo' => 0,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], $this->SUT->toArray());

        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function testPull()
    {
        $value = $this->SUT->pull('foo');
        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], $this->SUT->toArray());

        $this->assertSame(1, $value);
    }

    public function testDelete()
    {
        $result = $this->SUT->delete('foo');

        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], $this->SUT->toArray());

        // non-existing key is ignored
        $this->SUT->delete('non-existing');

        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], $this->SUT->toArray());
        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function testItShouldTransformImmutableToArgumentObject()
    {
        $SUT = new Arguments([]);
        $this->assertInstanceOf(Data::class, $SUT->toImmutable());
    }

    public function testMagicSetter()
    {
        $SUT = new Arguments(['foo' => 'bar']);
        $SUT->foo = 'qux';

        $this->assertSame('qux', $SUT->foo);
    }

    public function testIterator()
    {
        $SUT = new Arguments([1, 2, 3]);
        $this->assertSame([1, 2, 3], iterator_to_array($SUT));
    }

    protected function setUp()
    {
        $this->SUT = new Arguments([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ]);
    }
}
