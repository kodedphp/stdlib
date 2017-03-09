<?php

namespace Koded\Stdlib;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

    public function testValueFunction()
    {
        $value = value([1, 2, 3]);
        $this->assertInstanceOf(Immutable::class, $value);
        $this->assertSame([1, 2, 3], $value->toArray());
    }

    public function testValueFunction2()
    {
        $value = arguments([1, 2, 3]);
        $this->assertInstanceOf(Arguments::class, $value);
        $this->assertSame([1, 2, 3], $value->toArray());

        $value->foo = 'bar';
        $this->assertSame([1, 2, 3, 'foo' => 'bar'], $value->toArray());
    }

    public function testClean()
    {
        $value = clean('<script>');
        $this->assertSame('&lt;script&gt;', $value);
    }
}
