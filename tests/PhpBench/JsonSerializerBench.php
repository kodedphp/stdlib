<?php

namespace Tests\Koded\Stdlib\PhpBench;

use Koded\Stdlib\Serializer\JsonSerializer;

class JsonSerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new JsonSerializer;
    }
}
