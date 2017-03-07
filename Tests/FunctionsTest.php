<?php

namespace Koded\Stdlib;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{

    public function testValueFunction()
    {
        $value = value([1, 2, 3]);
        $this->assertInstanceOf(Immutable::class, $value);
        $this->assertSame([1, 2, 3], (array)$value);
        $this->assertSame([1, 2, 3], $value->toArray());
    }

    public function testValueFunction2()
    {
        $value = arguments([1, 2, 3]);
        $this->assertInstanceOf(Arguments::class, $value);
        $this->assertSame([1, 2, 3], (array)$value);
        $this->assertSame([1, 2, 3], $value->toArray());

        $value->foo = 'bar';
        $this->assertSame([1, 2, 3, 'foo' => 'bar'], (array)$value);
    }

    public function testClean()
    {
        $value = clean('<script>');
        $this->assertSame('&lt;script&gt;', $value);
    }
}
