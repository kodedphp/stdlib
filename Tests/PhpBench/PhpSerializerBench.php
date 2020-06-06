<?php

namespace Koded\Stdlib\Tests\PhpBench;

use Koded\Stdlib\Serializer\PhpSerializer;

class PhpSerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new PhpSerializer;
    }
}
