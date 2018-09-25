<?php

namespace Koded\Stdlib\Serializer;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the constructor.
 */
class JsonSerializerOptionsTest extends TestCase
{

    public function test_custom_options()
    {
        $expected = JSON_UNESCAPED_UNICODE;

        $this->assertAttributeEquals($expected, 'options', new JsonSerializer($expected));
    }

    public function test_default_options()
    {
        $expected = JSON_PRESERVE_ZERO_FRACTION
            | JSON_NUMERIC_CHECK
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_FORCE_OBJECT;

        $this->assertAttributeEquals($expected, 'options', new JsonSerializer($expected));
    }
}
