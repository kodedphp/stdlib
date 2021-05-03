<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer;
use Koded\Stdlib\Serializer\PhpSerializer;
use PHPUnit\Framework\TestCase;

class PhpSerializerTest extends TestCase
{
    private PhpSerializer $SUT;

    private mixed $original;
    private string $serialized;

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
        $this->assertSame(Serializer::PHP, $this->SUT->type());
    }

    protected function setUp(): void
    {
        $this->SUT = new PhpSerializer;
        $this->original = require __DIR__ . '/../fixtures/config-test.php';
        $this->serialized = \serialize($this->original);
    }
}
