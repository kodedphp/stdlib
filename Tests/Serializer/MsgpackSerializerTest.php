<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer;
use Koded\Stdlib\Serializer\MsgpackSerializer;
use PHPUnit\Framework\TestCase;

class MsgpackSerializerTest extends TestCase
{
    private MsgpackSerializer $SUT;

    private mixed $original;
    private $msg;

    public function test_serialize()
    {
        $this->assertEquals(\msgpack_pack($this->original), $this->SUT->serialize($this->original));
    }

    public function test_unserialize()
    {
        $this->assertEquals($this->original, $this->SUT->unserialize($this->msg));
    }

    public function testName()
    {
        $this->assertSame(Serializer::MSGPACK, $this->SUT->type());
    }

    protected function setUp(): void
    {
        if (false === \extension_loaded('msgpack')) {
            $this->markTestSkipped('msgpack extension is not loaded');
        }

        $this->SUT = new MsgpackSerializer;
        $this->original = require __DIR__ . '/../fixtures/config-test.php';
        $this->msg = \msgpack_pack($this->original);
    }
}
