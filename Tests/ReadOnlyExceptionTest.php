<?php

namespace Koded\Stdlib\Tests;

use Koded\Exceptions\ReadOnlyException;
use Koded\Stdlib\Data;
use PHPUnit\Framework\TestCase;

class ReadOnlyExceptionTest extends TestCase
{

    public function test_message_and_code()
    {
        $ex1 = ReadOnlyException::forCloning('Foo');
        $ex2 = ReadOnlyException::forInstance('foo', 'Bar');

        $this->assertEquals('Cloning the Foo instance is not allowed', $ex1->getMessage());
        $this->assertSame(Data::E_CLONING_DISALLOWED, $ex1->getCode());

        $this->assertEquals('Cannot set foo. Bar instance is read-only', $ex2->getMessage());
        $this->assertSame(Data::E_READONLY_INSTANCE, $ex2->getCode());
    }
}
