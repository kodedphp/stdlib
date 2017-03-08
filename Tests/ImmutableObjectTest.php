<?php

namespace Koded\Stdlib;

use Koded\Exceptions\ReadOnlyException;

class ImmutableObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Immutable
     */
    private $SUT;

    public function testShouldLoadTheArray()
    {
        $this->assertCount(8, $this->SUT);
    }

    public function testShouldDisallowCloning()
    {
        $this->expectException(ReadOnlyException::class);
        $this->expectExceptionMessage('Cloning the Koded\Stdlib\Immutable instance is not allowed');
        clone $this->SUT;
    }

    public function testShouldDisallowMagicallySettingValues()
    {
        $this->expectException(ReadOnlyException::class);
        $this->expectExceptionMessage('Koded\Stdlib\Immutable instance is read-only');
        $this->SUT->fubar = 'no';
    }

    public function testShouldTransformImmutableToArgumentObject()
    {
        $this->assertInstanceOf(Arguments::class, $this->SUT->toArgument());
    }

    public function testShouldGetThings()
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

    public function testShouldReturnNullOnNonExistingKey()
    {
        $this->assertNull($this->SUT->yabbadabbadoo);
        $this->assertNull($this->SUT->get('yabbadabbadoo'));
    }

    public function testShouldFindThings()
    {
        $this->assertEquals('not found', $this->SUT->find('key3', 'not found'));
        $this->assertSame('found me', $this->SUT->find('array.key3.key3-1.key3-1-1'));

        // find() fails if nested key has dot(s)
        $this->assertNull($this->SUT->find('array.key4.0'));

        // but it's fine if key is not nested
        $this->assertSame('four', $this->SUT->find('one.two.three'));
    }

    public function testShouldFilterOutTheData()
    {
        $expected = new Immutable([
            '0' => 0,
            'null' => null,
            'one.two.three' => 'four',
            true => true,

        ]);

        $this->assertEquals($expected, $this->SUT->extract([
            true,
            'one.two.three',
            '0',
            'null',
            'non-existing'
        ]));
    }

    protected function setUp()
    {
        $this->SUT = new Immutable(require __DIR__ . '/fixtures/nested_array.php');
    }
}
