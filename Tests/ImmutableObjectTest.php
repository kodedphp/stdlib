<?php

namespace Tests\Koded\Stdlib;

use Koded\Exceptions\ReadOnlyException;
use Koded\Stdlib\{Arguments, Immutable};
use PHPUnit\Framework\TestCase;

class ImmutableObjectTest extends TestCase
{
    private Immutable $SUT;

    public function test_should_load_the_array()
    {
        $this->assertCount(9, $this->SUT);
    }

    public function test_should_disallow_cloning()
    {
        $this->expectException(ReadOnlyException::class);
        $this->expectExceptionMessage('Cloning the Koded\Stdlib\Immutable instance is not allowed');
        clone $this->SUT;
    }

    public function test_should_disallow_magically_setting_values()
    {
        $this->expectException(ReadOnlyException::class);
        $this->expectExceptionMessage('Koded\Stdlib\Immutable instance is read-only');
        $this->SUT->fubar = 'no';
    }

    public function test_should_transform_immutable_to_argument_object()
    {
        $this->assertInstanceOf(Arguments::class, $this->SUT->toArguments());
    }

    public function test_should_get_things()
    {
        $this->assertNull($this->SUT->get('fubar'));
        $this->assertTrue($this->SUT->get(true));
        $this->assertTrue($this->SUT->has(null));
        $this->assertSame('bar', $this->SUT->foo);
        $this->assertSame(0, $this->SUT->get('0'));
        $this->assertSame(0, $this->SUT->{0});

        // because why not
        $this->assertSame(0, $this->SUT->get(0));

        // now strict has left the building
        $this->assertSame('null', $this->SUT->get(false));
    }

    public function test_should_return_null_on_non_existing_key()
    {
        $this->assertNull($this->SUT->yabbadabbadoo);
        $this->assertNull($this->SUT->get('yabbadabbadoo'));
    }

    public function test_should_find_things()
    {
        $this->assertEquals('not found', $this->SUT->find('key3', 'not found'));
        $this->assertSame('found me', $this->SUT->find('array.key3.key3-1.key3-1-1'));

        // find() fails if nested key has dot(s)
        $this->assertNull($this->SUT->find('array.key4.0'));

        // but it's fine if key is not nested
        $this->assertSame('four', $this->SUT->find('one.two.three'));

        // also NULL value
        $this->assertNull($this->SUT->find('one.null'));
    }

    public function test_should_filter_out_the_data()
    {
        $expected = [
            '0' => 0,
            'null' => null,
            'one.two.three' => 'four',
            true => true,
            'non-existing' => null,
            'one.null' => null

        ];

        $this->assertEquals($expected, $this->SUT->extract([
            true,
            'one.two.three',
            '0',
            'null',
            'non-existing',
            'one.null'
        ]));
    }

    public function test_equals_method()
    {
        $data = new Immutable([
            'e1' => null,
            'e2' => '',
            '01' => 0,
            '02' => '0',
            'foo1' => 'Foo',
            'foo2' => 'foo',
            'obj1' => new \stdClass,
            'obj2' => new \stdClass,
            'false' => false,
        ]);

        $this->assertFalse($data->equals('e1', 'e2'));
        $this->assertFalse($data->equals('01', '02'));
        $this->assertFalse($data->equals('foo1', 'foo2'));
        $this->assertFalse($data->equals('obj1', 'obj2'));
        $this->assertFalse($data->equals('false', '01'));
        $this->assertFalse($data->equals('false', '02'));

        // NULL checks
        $this->assertTrue($data->equals('e1', 'non-existent'));
        $this->assertTrue($data->equals('non-existent-1', 'non-existent-2'));
        $this->assertFalse($data->equals('e2', 'non-existent'));
    }

    protected function setUp(): void
    {
        $this->SUT = new Immutable(require __DIR__ . '/fixtures/nested-array.php');
    }
}
