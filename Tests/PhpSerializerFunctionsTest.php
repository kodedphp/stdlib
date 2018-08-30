<?php

namespace Koded\Stdlib;

use PHPUnit\Framework\TestCase;

class PhpSerializerFunctionsTest extends TestCase
{

    /** @var array */
    private $original;

    /** @var string */
    private $serialized;

    public function test_serialize_php()
    {
        $this->assertEquals($this->serialized, php_serialize($this->original));
    }

    public function test_unserialize_php()
    {
        $this->assertEquals($this->original, php_unserialize($this->serialized));
    }

    protected function setUp()
    {
        $this->original = require __DIR__ . '/fixtures/config-test.php';
        $this->serialized = php_serialize($this->original);
    }
}
