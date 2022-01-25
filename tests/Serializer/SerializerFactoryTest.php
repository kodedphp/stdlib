<?php

namespace Tests\Koded\Stdlib\Serializer;

use Koded\Exceptions\SerializerException;
use Koded\Stdlib\Serializer;
use Koded\Stdlib\Serializer\SerializerFactory;
use PHPUnit\Framework\TestCase;
use Tests\Koded\Stdlib\ObjectPropertyTrait;

class SerializerFactoryTest extends TestCase
{
    use ObjectPropertyTrait;

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
        if (false === \function_exists('igbinary_serialize')) {
            $this->markTestSkipped('igbinary extension is not loaded');
        }

        $igbinary = SerializerFactory::new(Serializer::IGBINARY);
        $this->assertSame(Serializer::IGBINARY, $igbinary->type());
    }

    public function test_msgpack()
    {
        if (false === \extension_loaded('msgpack')) {
            $this->markTestSkipped('msgpack extension is not loaded');
        }

        $msgpack = SerializerFactory::new(Serializer::MSGPACK);
        $this->assertSame(Serializer::MSGPACK, $msgpack->type());
    }

    public function test_custom()
    {
        $custom = SerializerFactory::new(TestSerializer::class);
        $this->assertSame(TestSerializer::class, $custom->type());

        $custom = new TestSerializer('foo', true);
        $this->assertSame('foo', $this->property($custom, 'arg1'));
        $this->assertSame(true, $this->property($custom, 'arg2'));

        $custom = new TestSerializer;
        $this->assertSame('', $this->property($custom, 'arg1'));
        $this->assertSame(false, $this->property($custom, 'arg2'));
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

        $this->assertSame(true, $this->property($json, 'associative'));
        $this->assertSame(
            JSON_PRESERVE_ZERO_FRACTION
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_PRETTY_PRINT
            | JSON_FORCE_OBJECT,
            $this->property($json, 'options')
        );
    }

    public function test_xml_factory_arguments()
    {
        $xml = SerializerFactory::new('xml', 'fubar');
        $this->assertSame('fubar', $this->property($xml, 'root'));

        $xml = SerializerFactory::new('xml');
        $this->assertSame(null, $this->property($xml, 'root'));

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
    public function serialize(mixed $value): ?string {}
    public function unserialize(string $value): mixed {}
    public function type(): string { return self::class; }
}
