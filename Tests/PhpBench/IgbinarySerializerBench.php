<?php

namespace Koded\Stdlib\Tests\PhpBench;

use Koded\Stdlib\Serializer\IgbinarySerializer;

class IgbinaryerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new IgbinarySerializer;
    }
}
