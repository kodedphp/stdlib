<?php

namespace Tests\Koded\Stdlib\PhpBench;

use Koded\Stdlib\Serializer\MsgpackSerializer;

class MsgpackSerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new MsgpackSerializer;
    }
}