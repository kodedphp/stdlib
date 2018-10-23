<?php

namespace Koded\Stdlib;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

    public function test_value_function()
    {
        $value = value([1, 2, 3]);
        $this->assertInstanceOf(Immutable::class, $value);
        $this->assertSame([1, 2, 3], $value->toArray());
    }

    public function test_arguments_function()
    {
        $value = arguments([1, 2, 3]);
        $this->assertInstanceOf(Arguments::class, $value);
        $this->assertSame([1, 2, 3], $value->toArray());

        $value->foo = 'bar';
        $this->assertSame([1, 2, 3, 'foo' => 'bar'], $value->toArray());
    }

    public function test_clean()
    {
        $value = clean('<script>');
        $this->assertSame('&lt;script&gt;', $value);
    }

    /**
     * @dataProvider camelCaseData
     */
    public function test_camel_case($string, $expected)
    {
        $this->assertSame($expected, snake_to_camel_case($string));
    }

    /**
     * @dataProvider snakeCaseData
     */
    public function test_snake_case($string, $expected)
    {
        $this->assertSame($expected, camel_to_snake_case($string));
    }

    /**
     * @dataProvider isAssociativeData
     */
    public function test_is_array_assoc_function($array, $expected)
    {
        $this->assertSame($expected, is_array_assoc($array));
    }

    /*
     *
     * Data providers
     *
     */

    public function camelCaseData()
    {
        return [
            [' excessive    Spaces  are    removed', 'ExcessiveSpacesAreRemoved'],
            ['all 123 numbers 456 are 789 preserved', 'All123Numbers456Are789Preserved'],
            ['the-dashes_and_underscores_are-removed ', 'TheDashesAndUnderscoresAreRemoved'],
            ['non alpha-numeric 4*z characters #1q ARE "removed"', 'NonAlphaNumeric4ZCharacters1qARERemoved'],
            ['th*1s-is%-ridic&&&&ulous', 'Th1sIsRidicUlous'],
        ];
    }

    public function snakeCaseData()
    {
        return [
            ['onlyCamelCaseStringMakesSense', 'only_camel_case_string_makes_sense'],
            ['This is NOT Converted as youThink', 'this_is_n_o_t_converted_as_you_think'],
            ['All123Numbers456Are789There', 'all123_numbers456_are789_there'],
            ['Non?AlphaNumeric4*XCharacters#1q"Are"mess', 'non_alpha_numeric4_x_characters1q_are_mess'],
            ['th*1s-is%-ridic&&&&ULous', 'th1s_is_ridic_u_lous'],
        ];
    }

    public function isAssociativeData()
    {
        return [
            // All of these are considered associative
            [[2 => 0, 0 => 1, 1 => 2], true],
            [[0 => 0, 1 => 1, 2 => 2], true],
            [['2' => 0, '0' => 1, '1' => 2], true],
            [['0' => 0, '1' => 1, '2' => 2], true],
            [[0 => 1, '1' => 1, 1 => 1], true],
            [['0' => 0], true],

            // These are sequential
            [['val0', 'val1', 'val2'], false],
            [['0', '1', '2'], false],
            [['"val1"'], false],
        ];
    }
}
