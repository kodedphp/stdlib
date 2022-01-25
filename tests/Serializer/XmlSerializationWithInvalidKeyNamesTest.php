<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer\XmlSerializer;
use PHPUnit\Framework\TestCase;

class XmlSerializationWithInvalidKeyNamesTest extends TestCase
{
    private XmlSerializer $serializer;

    private string $xml = <<<XML
<?xml version="1.0"?>
<response xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <explain>
        <item key="/geo/ip\\.(json|xml)">Returns the client IP geo information</item>
        <item key="/geo/(IP_ADDRESS).(json|xml)">Returns the IP address geo information</item>
        <item key="/geo/">The &lt;index&gt; route</item>
    </explain>
 </response>
XML;

    private array $data = [
        'explain' => [
            '/geo/ip\.(json|xml)' => 'Returns the client IP geo information',
            '/geo/(IP_ADDRESS).(json|xml)' => 'Returns the IP address geo information',
            '/geo/' => 'The <index> route',
        ]
    ];

    public function test_bug_for_serialization()
    {
        $this->assertXmlStringEqualsXmlString(
            $this->xml,
            $this->serializer->serialize($this->data)
        );
    }

    public function test_bug_for_deserialization()
    {
        $this->assertTrue(
            $this->data === $this->serializer->unserialize($this->xml)
        );
    }

    protected function setUp(): void
    {
        $this->serializer = new XmlSerializer('response');
    }
}
