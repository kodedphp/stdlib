<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\{Arguments, Immutable};
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\{arguments,
    camel_to_snake_case,
    htmlencode,
    is_associative,
    now,
    randomstring,
    snake_to_camel_case,
    to_delimited_string,
    to_kebab_string,
    value,
    rmdir};

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

    public function test_htmlescape_function()
    {
        $value = htmlencode('<script>');
        $this->assertSame('&lt;script&gt;', $value);
    }

    /**
     * @dataProvider camelCaseData
     */
    public function test_camel_case_function($string, $expected)
    {
        $this->assertSame($expected, snake_to_camel_case($string));
    }

    /**
     * @dataProvider snakeCaseData
     */
    public function test_snake_case_function($string, $expected)
    {
        $this->assertSame($expected, camel_to_snake_case($string));
    }

    /**
     * @dataProvider isAssociativeArrayData
     */
    public function test_is_array_assoc_function($array, $expected)
    {
        $this->assertSame($expected, is_associative($array));
    }

    public function test_now_function()
    {
        $now = now();
        $this->assertSame('UTC', $now->getTimezone()->getName());

        $timestamp = now()->getTimestamp();
        $duration = new \DateInterval('PT12H');

        $now->add($duration);
        $this->assertEquals($timestamp, $now->getTimestamp(), 'The DateTime object is not changed');

        $after12hours = $now->add($duration);
        $this->assertNotSame($now, $after12hours, 'now() is immutable');
        $this->assertEquals(60 * 60 * 12, $after12hours->getTimestamp() - $timestamp);
    }


    public function test_rmdir_function()
    {
        $dir = \sys_get_temp_dir() . DIRECTORY_SEPARATOR. randomstring(9);
        $file = $dir . DIRECTORY_SEPARATOR . randomstring(9) . '.txt';

        $this->assertTrue(mkdir($dir), 'Should create an empty directory');
        $this->assertSame(4, \file_put_contents($file, 'test'), 'Should create the file in the directory');
        $this->assertFileExists($file, 'Should add file in the directory');

        $this->assertTrue(rmdir($dir));
        $this->assertFileDoesNotExist($file, 'Directories with files are deleted');

        $this->assertDirectoryDoesNotExist($dir . '/foo');
        $this->assertTrue(rmdir($dir), 'The non-existent directories are not processed at all');
    }


    /**
     * @dataProvider forDelimiterTest
     */
    public function test_to_delimited_string($string, $delimiter, $expected)
    {
        $this->assertSame($expected, to_delimited_string($string, ord($delimiter)));
    }

    /**
     * @dataProvider forKebabTest
     */
    public function test_to_kebab_string($string, $expected)
    {
        $this->assertSame($expected, to_kebab_string($string));
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

    public function isAssociativeArrayData()
    {
        return [
            // All of these are considered associative

            [['key' => 'val'], true],
            [[1 => 'val'], true],
            [[2 => 0, 0 => 1, 1 => 2], true],
            [['2' => 0, '0' => 1, '1' => 2], true],
            [[0 => 1, 3 => 3, 4 => 4], true],

            // These are sequential

            [[], false],
            [[''], false],
            [[0], false],
            [[1], false],
            [[0 => 0], false],
            [[0 => 1], false],
            [['0', '1', '2'], false],
            [['0'], false],
            [[1.0], false],
            [[0, 3], false],
            [['val0'], false],
            [['val0', 'val1', 'val2'], false],
            [[0 => 0, 1 => 1, 2 => 2], false],

            // The unfortunate string-to-integer internal convert

            [['0' => false], false],
            [['0' => 0, '1' => 1, '2' => 2], false],
            [[0 => 1, '1' => 1, 2 => 1], false],

            // None of the keys are valid or sane, but it "works" because PHP

            [[null => 1], true],    // NULL is converted to ''
            [[false => 1], false],  // FALSE is converted to 0
            [[true => 1], true],    // TRUE is converted to 1

            [[2.7 => 'yes'], true], // FLOAT is a different level of weird (implicit float-to-string or int conversion)
        ];
    }

    public function forDelimiterTest()
    {
        return [
            ["Hello fri3nd, you're
       looking          good today!", '.', "Hello.fri3nd.you're.looking.good.today."],
            ["Hello fri3nd, you're
       looking          good today!", '_', "Hello_fri3nd_you're_looking_good_today_"],

        ];
    }

    public function forKebabTest()
    {
        return [
            ['this_is_converted to kebab_Case', 'this-is-converted-to-kebab-case']
        ];
    }

}
