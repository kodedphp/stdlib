<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer;
use Koded\Stdlib\Serializer\IgbinarySerializer;
use PHPUnit\Framework\TestCase;

class IgbinarySerializerTest extends TestCase
{
    private IgbinarySerializer $SUT;
    private mixed $original;
    private ?string $igb;

    public function test_serialize()
    {
        $this->assertEquals(\igbinary_serialize($this->original), $this->SUT->serialize($this->original));
    }

    public function test_unserialize()
    {
        $this->assertEquals($this->original, $this->SUT->unserialize($this->igb));
    }

    public function testName()
    {
        $this->assertSame(Serializer::IGBINARY, $this->SUT->type());
    }

    protected function setUp(): void
    {
        if (false === \function_exists('igbinary_serialize')) {
            $this->markTestSkipped('igbinary extension is not loaded');
        }

        $this->SUT = new IgbinarySerializer;
        $this->original = require __DIR__ . '/../fixtures/config-test.php';
        $this->igb = \igbinary_serialize($this->original);
    }
}
