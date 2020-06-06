<?php

namespace Koded\Stdlib\Tests;

use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\randomstring;

class RandomAlphaNumericFunctionTest extends TestCase
{

    public function test_random_alpha_numeric_with_default_number()
    {
        $random = randomstring();
        $this->assertSame(16, strlen($random));
        $this->assertRegExp('~[a-z0-9]~i', $random);
    }

    public function test_random_alpha_numeric_with_prefix()
    {
        $random = randomstring(64, 'ABC');
        $this->assertSame(67, strlen($random));
        $this->assertSame('ABC', substr($random, 0, 3));
    }

    public function test_random_alpha_numeric_with_suffix()
    {
        $random = randomstring(128, '', 'ABC');
        $this->assertSame(131, strlen($random));
        $this->assertSame('ABC', substr($random, -3));
    }

    public function test_random_alpha_numeric_with_prefix_and_suffix()
    {
        $random = randomstring(4, 'ABC-', '-XYZ');
        $this->assertSame(12, strlen($random));
        $this->assertRegExp('~ABC\-[a-z0-9]{4}\-XYZ~i', $random);
    }
}
