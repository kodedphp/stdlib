<?php

namespace Koded\Stdlib\Tests\Serializer;

use Koded\Exceptions\SerializerException;
use Koded\Stdlib\Serializer;
use Koded\Stdlib\Serializer\SerializerFactory;
use PHPUnit\Framework\TestCase;

class SerializerFactoryTest extends TestCase
{

    public function test_native()
    {
        $json = SerializerFactory::new(Serializer::JSON);
        $xml = SerializerFactory::new(Serializer::XML, 'root');
        $php = SerializerFactory::new(Serializer::PHP);

        $this->assertSame(Serializer::JSON, $json->type());
        $this->assertSame(Serializer::XML, $xml->type());
        $this->assertSame(Serializer::PHP, $php->type());
    }

    public function test_igbinary()
    {
        if (false === function_exists('igbinary_serialize')) {
            $this->markTestSkipped('igbinary extension is not loaded');
        }

        $igbinary = SerializerFactory::new(Serializer::IGBINARY);
        $this->assertSame(Serializer::IGBINARY, $igbinary->type());
    }

    public function test_msgpack()
    {
        if (false === extension_loaded('msgpack')) {
            $this->markTestSkipped('msgpack extension is not loaded');
        }

        $msgpack = SerializerFactory::new(Serializer::MSGPACK);
        $this->assertSame(Serializer::MSGPACK, $msgpack->type());
    }

    public function test_custom()
    {
        $serializer = SerializerFactory::new(TestSerializer::class);
        $this->assertSame(TestSerializer::class, $serializer->type());
    }

    public function test_exception()
    {
        $this->expectException(SerializerException::class);
        $this->expectExceptionCode(409);
        $this->expectExceptionMessage('Failed to create a serializer for "fubar"');

        SerializerFactory::new('fubar');
    }

    public function test_json_factory_arguments()
    {
        $options = JSON_PRETTY_PRINT | JSON_FORCE_OBJECT ^ JSON_THROW_ON_ERROR;
        $json = SerializerFactory::new('json', $options, true);

        $this->assertAttributeSame(true, 'associative', $json);
        $this->assertAttributeSame(
            JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_FORCE_OBJECT,
            'options',
            $json
        );
    }

    public function test_xml_factory_arguments()
    {
        $xml = SerializerFactory::new('xml', 'fubar');
        $this->assertAttributeSame('fubar', 'root', $xml);
    }

    public function test_custom_serializer()
    {
        $custom = new TestSerializer('foo', true);

        $this->assertAttributeSame('foo', 'arg1', $custom);
        $this->assertAttributeSame(true, 'arg2', $custom);
    }
}


class TestSerializer implements Serializer
{
    private $arg1;
    private $arg2;

    public function __construct(string $arg1 = '', bool $arg2 = false)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
    public function serialize($value): string {}
    public function unserialize($value) {}
    public function type(): string { return self::class; }
}
