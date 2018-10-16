<?php

namespace Koded\Exceptions;

use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{

    public function testSerializerExceptionforMissingModule()
    {
        $this->expectException(SerializerException::class);
        $this->expectExceptionMessage('[Dependency error] "fubar" module is not installed on this machine');
        $this->expectExceptionCode(424);
        throw SerializerException::forMissingModule('fubar');
    }

    public function testSerializerExceptionforCreate()
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
        $this->expectExceptionCode(1002);
        throw KodedException::generic('hello');
    }
}
