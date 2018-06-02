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
            ['This is NOT Converted as youThink', 'this is n_o_t converted as you_think'],
            ['All123Numbers456Are789There', 'all123_numbers456_are789_there'],
            ['Non?AlphaNumeric4*XCharacters#1q"Are"mess', 'non?alpha_numeric4*x_characters#1q"are"mess'],
            ['th*1s-is%-ridic&&&&ULous', 'th*1s-is%-ridic&&&&u_lous'],
        ];
    }
}
