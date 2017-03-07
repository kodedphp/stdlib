<?php

namespace Koded\Stdlib;

use Koded\Stdlib\Interfaces\Argument;
use Koded\Stdlib\Interfaces\Data;

class ArgumentObjectTest extends \PHPUnit_Framework_TestCase
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
        ], (array)$this->SUT);

        $result = $this->SUT->set('foo', 'gir');

        $this->assertEquals([
            'foo' => 'gir',
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ], (array)$this->SUT);

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
        ], (array)$this->SUT);
        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function test_upsert()
    {
        // not added because it already exist
        $result = $this->SUT->upsert('foo', 42);

        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ], (array)$this->SUT);

        // added because it does not exist
        $this->SUT->upsert('poo', 42);

        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
            'poo' => 42
        ], (array)$this->SUT);

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
        ], (array)$this->SUT);

        $var = 0;
        $this->assertEquals([
            'foo' => 0,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], (array)$this->SUT);

        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function testPull()
    {
        $value = $this->SUT->pull('foo');
        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], (array)$this->SUT);

        $this->assertSame(1, $value);
    }

    public function testDelete()
    {
        $result = $this->SUT->delete('foo');

        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], (array)$this->SUT);

        // non-existing key is ignored
        $this->SUT->delete('non-existing');

        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], (array)$this->SUT);
        $this->assertInstanceOf(Arguments::class, $result);
    }

    public function testItShouldTransformImmutableToArgumentObject()
    {
        $SUT = new Arguments([]);
        $this->assertInstanceOf(Data::class, $SUT->toImmutable());
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
