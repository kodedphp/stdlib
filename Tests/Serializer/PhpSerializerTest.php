<?php

namespace Koded\Stdlib\Serializer;

use Koded\Stdlib\Interfaces\Serializer;
use PHPUnit\Framework\TestCase;

class PhpSerializerTest extends TestCase
{

    /** @var PhpSerializer */
    private $SUT;

    private $original;
    private $serialized;

    public function test_serialize()
    {
        $this->assertEquals($this->serialized, $this->SUT->serialize($this->original));
    }

    public function test_unserialize()
    {
        $this->assertEquals($this->original, $this->SUT->unserialize($this->serialized));
    }

    public function testName()
    {
        $this->assertSame(Serializer::PHP, $this->SUT->name());
    }

    protected function setUp()
    {
        $this->SUT = new PhpSerializer;
        $this->original = require __DIR__ . '/../fixtures/config-test.php';
        $this->serialized = serialize($this->original);
    }
}
