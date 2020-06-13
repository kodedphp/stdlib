<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Stdlib\Serializer\XmlSerializer;
use PHPUnit\Framework\TestCase;

class XmlAtomSerializerTest extends TestCase
{
    private const ATOM_EXAMPLE = __DIR__ . '/../fixtures/atom-example.xml';

    /** @var XmlSerializer */
    private $serializer;

    public function test_unserialize_atom_file()
    {
        $atom = $this->serializer->unserialize(file_get_contents(self::ATOM_EXAMPLE));
        $channel = $atom['channel'] ?? [];

        $this->assertArrayHasKey('title', $channel);
        $this->assertArrayHasKey('link', $channel);
        $this->assertArrayHasKey('description', $channel);
        $this->assertArrayHasKey('lastBuildDate', $channel);
        $this->assertArrayHasKey('language', $channel);
        $this->assertArrayHasKey('generator', $channel);
        $this->assertArrayHasKey('image', $channel);

        $this->assertSame([
            '@rel'  => 'self',
            '@type' => 'application/rss+xml',
            '@href' => 'http://example.com',
            '#' => '',
        ], $channel['atom10:link'], 'XML node attributes are parsed, regardless of the value');

        // items list and item structure
        $this->assertCount(3, $channel['item']);

        $item = $channel['item'][0];
        $this->assertArrayHasKey('title', $item);
        $this->assertArrayHasKey('link', $item);
        $this->assertArrayHasKey('pubDate', $item);
        $this->assertArrayHasKey('dc:creator', $item);
        $this->assertArrayHasKey('category', $item);
        $this->assertArrayHasKey('guid', $item);
        $this->assertArrayHasKey('description', $item);
        $this->assertArrayHasKey('content:encoded', $item);
    }

    protected function setUp(): void
    {
        $this->serializer = new XmlSerializer('payload');
    }
}
