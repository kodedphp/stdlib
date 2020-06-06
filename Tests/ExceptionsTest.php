<?php

namespace Koded\Stdlib\Tests;

use Koded\Exceptions\{KodedException, SerializerException};
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{

    public function testSerializerExceptionForMissingModule()
    {
        $this->expectException(SerializerException::class);
        $this->expectExceptionMessage('[Dependency error] "fubar" module is not installed on this machine');
        $this->expectExceptionCode(424);
        throw SerializerException::forMissingModule('fubar');
    }

    public function testSerializerExceptionForCreate()
    {
        $this->expectException(SerializerException::class);
        $this->expectExceptionMessage('Failed to create a serializer for "fubar"');
        $this->expectExceptionCode(409);
        throw SerializerException::forCreateSerializer('fubar');
    }

    public function testGenericException()
    {
        $this->expectException(KodedException::class);
        $this->expectExceptionMessage('hello');
        $this->expectExceptionCode(1003);
        throw KodedException::generic('hello');
    }

    public function testCreateExceptionFromOtherException()
    {
        $this->expectException(KodedException::class);
        $this->expectExceptionMessage('test');
        $this->expectExceptionCode(123);

        $other = new \OutOfBoundsException('test', 123);
        throw KodedException::from($other);
    }

    public function testCreateExceptionFromError()
    {
        $this->expectException(KodedException::class);
        $this->expectExceptionMessage('test');
        $this->expectExceptionCode(1);

        $other = new \Error('test', 1);
        throw KodedException::from($other);
    }
}
