<?php

namespace Koded\Stdlib;

use Koded\Exceptions\ReadOnlyException;
use Koded\Stdlib\Interfaces\Data;
use PHPUnit\Framework\TestCase;

class ReadOnlyExceptionTest extends TestCase
{

    public function testMessageAndCode()
    {
        $ex1 = new ReadOnlyException(Data::E_CLONING_DISALLOWED, [':class' => 'Foo']);
        $ex2 = new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => 'Bar']);

        $this->assertEquals('Cloning the Foo instance is not allowed', $ex1->getMessage());
        $this->assertSame(Data::E_CLONING_DISALLOWED, $ex1->getCode());

        $this->assertEquals('Bar instance is read-only', $ex2->getMessage());
        $this->assertSame(Data::E_READONLY_INSTANCE, $ex2->getCode());
    }
}
