<?php

namespace Koded\Stdlib\Tests\PhpBench;

use Koded\Stdlib\Serializer\XmlSerializer;

class XmlSerializerBench extends AbstractSerializerBench
{
    public function setUp(): void
    {
        $this->serializer = new XmlSerializer('payload');
    }
}
