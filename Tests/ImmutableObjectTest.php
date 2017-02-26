<?php

namespace Koded\Stdlib;

use LogicException;

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
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cloning the Koded\Stdlib\Immutable is not allowed');
        clone $this->SUT;
    }

    public function testShouldDisallowAppendingValues()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Koded\Stdlib\Immutable instance is read-only');
        $this->SUT->append('test');
    }

    public function testShouldDisallowSettingValues()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Koded\Stdlib\Immutable instance is read-only');
        $this->SUT->offsetSet('test', 'test');
    }

    public function testShouldDisallowUnsettingValues()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Koded\Stdlib\Immutable instance is read-only');
        $this->SUT->offsetUnset('foo');
    }

    public function testShouldDisallowReplacingTheInternalStore()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Koded\Stdlib\Immutable instance is read-only');
        $this->SUT->exchangeArray([]);
    }

    /*public function testShouldTransformImmutableToArgumentObject()
    {
        $this->assertInstanceOf(Argument::class, $this->SUT->toArgument());
    }*/

    public function testGet()
    {
        $this->assertTrue($this->SUT->get(true));
        $this->assertTrue(array_key_exists(null, $this->SUT));
        $this->assertSame('bar', $this->SUT->foo);
        $this->assertSame(0, $this->SUT->get('0'));
        $this->assertSame(0, $this->SUT->{0});

        // because why not
        $this->assertSame(0, $this->SUT->get(0));

        // now strict has left the building
        $this->assertSame('null', $this->SUT->get(false));
    }

    public function testFind()
    {
        $this->assertEquals('not found', $this->SUT->find('key3', 'not found'));
        $this->assertSame('found me', $this->SUT->find('array.key3.key3-1.key3-1-1'));

        // find() fails if nested key has dot(s)
        $this->assertNull($this->SUT->find('array.key4.0'));

        // but it's fine if key is not nested
        $this->assertSame('four', $this->SUT->find('one.two.three'));
    }

    public function testFilter()
    {
        $expected = new Immutable([
            '0' => 0,
            'null' => null,
            'one.two.three' => 'four',
            true => true,

        ]);

        $this->assertEquals($expected, $this->SUT->filter([true, 'one.two.three', '0', 'null']));
    }

    protected function setUp()
    {
        $this->SUT = new Immutable(require __DIR__ . '/fixtures/nested_array.php');
    }
}
