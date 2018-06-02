<?php

namespace Koded\Stdlib;

use Koded\Exceptions\ReadOnlyException;
use PHPUnit\Framework\TestCase;

class ImmutableObjectTest extends TestCase
{

    /**
     * @var Immutable
     */
    private $SUT;

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
        $this->assertInstanceOf(Arguments::class, $this->SUT->toArgument());
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

    protected function setUp()
    {
        $this->SUT = new Immutable(require __DIR__ . '/fixtures/nested_array.php');
    }
}
