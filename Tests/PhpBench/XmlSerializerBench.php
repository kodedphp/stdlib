<?php

namespace Tests\Koded\Stdlib\PhpBench;

use Koded\Stdlib\Serializer\XmlSerializer;

class XmlSerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new XmlSerializer('payload');
    }
}
