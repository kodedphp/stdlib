<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\{Arguments, Data};
use PHPUnit\Framework\TestCase;

class ArgumentObjectTest extends TestCase
{
    private Arguments $SUT;

    public function test_set()
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
     * @depends test_set
     */
    public function test_import()
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

    public function test_upsert()
    {
        // Not added because 'foo' already exist
        $result = $this->SUT->upsert('foo', 42);

        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ], $this->SUT->toArray());

        // Added because it does not exist
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

    public function test_bind()
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

    public function test_pull()
    {
        $value = $this->SUT->pull('foo');
        $this->assertEquals([
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim',
        ], $this->SUT->toArray());

        $this->assertSame(1, $value);
    }

    public function test_delete()
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

    public function test_it_should_transform_immutable_to_argument_object()
    {
        $SUT = new Arguments([]);
        $this->assertInstanceOf(Data::class, $SUT->toImmutable());
    }

    public function test_magic_setter()
    {
        $SUT = new Arguments(['foo' => 'bar']);
        $SUT->foo = 'qux';

        $this->assertSame('qux', $SUT->foo);
    }

    public function test_iterator()
    {
        $SUT = new Arguments([1, 2, 3]);
        $this->assertSame([1, 2, 3], iterator_to_array($SUT));
    }

    public function test_cloning()
    {
        $clone = clone $this->SUT;
        $this->assertEquals($this->SUT, $clone);
        $this->assertNotSame($this->SUT, $clone);
    }

    public function test_should_clear_the_storage()
    {
        $SUT = new Arguments(['foo' => 'bar']);
        $this->assertCount(1, $SUT);

        $SUT->clear();
        $this->assertCount(0, $SUT);
    }

    public function test_should_filter_the_data()
    {
        $SUT = new Arguments([
            'FOO.FOO' => 'foo',
            'FOO.BAR' => 'bar',
            'gir' => 'qux',
        ]);

        $this->assertEquals(new Arguments([
            'FOO' => 'foo',
            'BAR' => 'bar',
            'gir' => 'qux'
        ]), $SUT->namespace('FOO.', false));
    }

    public function test_should_filter_prefixed_indexes()
    {
        $SUT = new Arguments([
            'foo' => 'foo',
            'foo.bar' => 'bar',
            'foo.FOO' => 'DANG!', // this one overrides the non-prefixed
            'gir' => 'qux',
        ]);

        $this->assertEquals(new Arguments([
            'foo' => 'DANG!',
            'bar' => 'bar',
            'gir' => 'qux'
        ]), $SUT->namespace('foo.'));
    }

    /**
     * Tests indirect array modification introduced in 7.1.4+
     */
    public function test_expected_dynamic_object_property_setter()
    {
        $this->SUT->schizo = 'phrenic';
        $this->assertFalse(empty($this->SUT->schizo));
        $this->assertSame('phrenic', $this->SUT->schizo);
    }

    public function test_indirect_array_modification()
    {
        $args = new Arguments;
        $args->indirect['modification']['BC'] = 'break';

        $this->assertSame(['indirect' => ['modification' => ['BC' => 'break']]], $args->toArray());
    }

    protected function setUp(): void
    {
        $this->SUT = new Arguments([
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'qux' => 'zim'
        ]);
    }
}
