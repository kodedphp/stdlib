<?php

namespace Tests\Koded\Stdlib;

use Koded\Stdlib\ExtendedArguments;
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\extended;

class ExtendedArgumentsFunctionTest extends TestCase
{
    public function test_extended_arguments_function()
    {
        $actual = [
            'foo.bar' => [1, 2, 3]
        ];
        $expected = [
            'foo' => [
                'bar' => [1, 2, 3]
            ]
        ];

        $data = extended($actual);
        $this->assertInstanceOf(ExtendedArguments::class, $data);
        $this->assertSame($expected, $data->toArray());

        $this->assertSame(3, $data->get('foo.bar.2'));
        $this->assertSame(['foo.bar.2' => 3], $data->extract(['foo.bar.2']));

        // mutate the value
        $data->foo = 'bar';
        $this->assertSame(['foo' => 'bar'], $data->toArray());
    }

}
