<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Stdlib\Serializer\XmlSerializer;
use PHPUnit\Framework\TestCase;

class XmlWithDifferentValueKeyTest extends TestCase
{
    public function test_unserialize_atom_file()
    {
        $serializer = new XmlSerializer('payload', '$val');
        $atom = $serializer->unserialize(file_get_contents(__DIR__ . '/../fixtures/atom-example.xml'));
        $channel = $atom['channel'] ?? [];

        $this->assertSame([
            '@rel'  => 'self',
            '@type' => 'application/rss+xml',
            '@href' => 'http://example.com',
            '$val' => '',
        ], $channel['atom10:link'], 'The key name for the value is changed');
    }
}
