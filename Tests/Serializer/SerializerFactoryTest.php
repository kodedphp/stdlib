<?php

namespace Koded\Stdlib\Serializer;

use Koded\Exceptions\SerializerException;
use Koded\Stdlib\Interfaces\Serializer;
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
        if (false === function_exists('msgpack_pack')) {
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
}


class TestSerializer implements Serializer
{
    public function serialize($value): string {}
    public function unserialize($value) {}
    public function type(): string { return self::class; }
}
