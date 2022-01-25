<?php

namespace Tests\Koded\Stdlib\PhpBench;

use Koded\Stdlib\Serializer\PhpSerializer;

class PhpSerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new PhpSerializer;
    }
}
