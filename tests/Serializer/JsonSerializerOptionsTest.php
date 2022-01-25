<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;
use Tests\Koded\Stdlib\ObjectPropertyTrait;

/**
 * Tests for the constructor.
 */
class JsonSerializerOptionsTest extends TestCase
{
    use ObjectPropertyTrait;

    public function test_default_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_THROW_ON_ERROR;

        $this->assertEquals(
            $expected,
            $this->property(new JsonSerializer, 'options')
        );
    }

    public function test_adding_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_PRETTY_PRINT
            | JSON_ERROR_INF_OR_NAN
            | JSON_THROW_ON_ERROR;

        $this->assertEquals(
            $expected,
            $this->property(new JsonSerializer(JSON_ERROR_INF_OR_NAN | JSON_PRETTY_PRINT), 'options')
        );
    }

    public function test_excluding_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE;

        $this->assertEquals(
            $expected,
            $this->property(new JsonSerializer(JSON_UNESCAPED_SLASHES ^ JSON_THROW_ON_ERROR), 'options')
        );
    }

    public function test_include_and_exclude_options()
    {
        $expected = JSON_NUMERIC_CHECK
            | JSON_UNESCAPED_SLASHES
            | JSON_THROW_ON_ERROR;

        $this->assertEquals($expected, $this->property(new JsonSerializer(
            JSON_UNESCAPED_UNICODE ^ JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK
        ), 'options'), 'Adds JSON_NUMERIC_CHECK, but removes JSON_UNESCAPED_UNICODE and JSON_PRESERVE_ZERO_FRACTION');

        $this->assertEquals($expected, $this->property(new JsonSerializer(
            JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK ^ JSON_UNESCAPED_UNICODE
        ), 'options'), 'Adds JSON_NUMERIC_CHECK, but removes JSON_UNESCAPED_UNICODE and JSON_PRESERVE_ZERO_FRACTION');

        $this->assertEquals($expected, $this->property(new JsonSerializer(
            JSON_NUMERIC_CHECK ^ JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE
        ), 'options'), 'Adds JSON_NUMERIC_CHECK, but removes JSON_UNESCAPED_UNICODE and JSON_PRESERVE_ZERO_FRACTION');
    }
}
