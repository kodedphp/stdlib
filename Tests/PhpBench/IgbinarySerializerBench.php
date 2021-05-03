<?php

namespace Tests\Koded\Stdlib\PhpBench;

use Koded\Stdlib\Serializer\IgbinarySerializer;

class IgbinaryerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new IgbinarySerializer;
    }
}
