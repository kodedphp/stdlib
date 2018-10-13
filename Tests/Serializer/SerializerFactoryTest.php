<?php

namespace Koded\Stdlib\Serializer;

use Koded\Exceptions\SerializerException;
use Koded\Stdlib\Interfaces\StringSerializable;
use PHPUnit\Framework\TestCase;

class SerializerFactoryTest extends TestCase
{

    public function test_native()
    {
        $json = SerializerFactory::new(SerializerFactory::JSON);
        $xml = SerializerFactory::new(SerializerFactory::XML, 'root');
        $php = SerializerFactory::new(SerializerFactory::PHP);

        $this->assertSame(SerializerFactory::JSON, $json->name());
        $this->assertSame(SerializerFactory::XML, $xml->name());
        $this->assertSame(SerializerFactory::PHP, $php->name());
    }

    public function test_igbinary()
    {
        if (false === function_exists('igbinary_serialize')) {
            $this->markTestSkipped('igbinary extension is not loaded');
        }

        $igbinary = SerializerFactory::new(SerializerFactory::IGBINARY);
        $this->assertSame(SerializerFactory::IGBINARY, $igbinary->name());
    }

    public function test_msgpack()
    {
        if (false === function_exists('msgpack_pack')) {
            $this->markTestSkipped('msgpack extension is not loaded');
        }

        $msgpack = SerializerFactory::new(SerializerFactory::MSGPACK);
        $this->assertSame(SerializerFactory::MSGPACK, $msgpack->name());
    }

    public function test_custom()
    {
        $serializer = SerializerFactory::new(TestSerializer::class);
        $this->assertSame(TestSerializer::class, $serializer->name());
    }

    public function test_exception()
    {
        $this->expectException(SerializerException::class);
        $this->expectExceptionCode(409);
        $this->expectExceptionMessage('Failed to create a serializer for "fubar"');

        SerializerFactory::new('fubar');
    }
}


class TestSerializer implements StringSerializable
{
    public function serialize($value): string
    {
    }

    public function unserialize(string $value)
    {
    }

    public function name(): string
    {
        return self::class;
    }
}
