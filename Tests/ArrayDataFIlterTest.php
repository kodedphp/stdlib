<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\Arguments;
use PHPUnit\Framework\TestCase;

class ArrayDataFIlterTest extends TestCase
{
    /**
     * @dataProvider iterableData
     */
    public function test_iterable_data($data)
    {
        $object = new Arguments;

        $this->assertEquals(['key0' => 'val0'], $object->filter($data, 'prefix.'));
    }

    public function iterableData()
    {
        $gen = function() {
            yield 'prefix.key0' => 'val0';
        };

        return [
            [['prefix.key0' => 'val0']],
            [$gen()],
        ];
    }
}
