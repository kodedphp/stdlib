<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the constructor.
 */
class JsonSerializerOptionsTest extends TestCase
{
    public function test_default_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

        $this->assertAttributeEquals($expected, 'options', new JsonSerializer);
    }

    public function test_adding_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION
            | JSON_UNESCAPED_SLASHES
            | JSON_PRETTY_PRINT
            | JSON_ERROR_INF_OR_NAN
            | JSON_THROW_ON_ERROR;

        $this->assertAttributeEquals($expected, 'options', new JsonSerializer(JSON_ERROR_INF_OR_NAN | JSON_PRETTY_PRINT));
    }

    public function test_excluding_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION;

        $this->assertAttributeEquals($expected, 'options', new JsonSerializer(
            JSON_UNESCAPED_SLASHES ^ JSON_THROW_ON_ERROR
        ));
    }

    public function test_include_and_exclude_options()
    {
        $expected = JSON_NUMERIC_CHECK
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_THROW_ON_ERROR;

        $this->assertAttributeEquals($expected, 'options', new JsonSerializer(
            JSON_UNESCAPED_UNICODE ^ JSON_PRESERVE_ZERO_FRACTION | JSON_NUMERIC_CHECK
        ), 'Adds JSON_UNESCAPED_UNICODE and JSON_NUMERIC_CHECK -- removes JSON_PRESERVE_ZERO_FRACTION');
    }
}
