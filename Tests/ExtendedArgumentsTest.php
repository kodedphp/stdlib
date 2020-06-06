<?php

namespace Koded\Stdlib\Tests;

use Koded\Stdlib\ExtendedArguments;
use PHPUnit\Framework\TestCase;

class ExtendedArgumentsTest extends TestCase
{
    /**
     * @dataProvider data
     */
    public function test_should_flatten_the_array($data)
    {
        $expected = new ExtendedArguments([
            'key1' => 'A',
            'key2.key21' => 'B',
            'key4.key41.key411' => 'D',
            'key4.key41.key412' => 'E',
            'key4.key42' => 'F',
            'key3.0' => 'A',
            'key3.1' => 'B',
            'key3.2' => 'C',
            'key5' => null,
        ]);

        $arguments = new ExtendedArguments($data);
        $this->assertEquals($expected, $arguments->flatten());
    }

    /**
     * @dataProvider data
     */
    public function test_get($data)
    {
        $arguments = new ExtendedArguments($data);

        $this->assertSame('A', $arguments->get('key1'));
        $this->assertSame('B', $arguments->get('key2.key21'));
        $this->assertSame(['key21' => 'B'], $arguments->get('key2'));
        $this->assertSame(['A', 'B', 'C'], $arguments->get('key3'));
        $this->assertSame('D', $arguments->get('key4.key41.key411'));
        $this->assertSame(['key411' => 'D', 'key412' => 'E'], $arguments->get('key4.key41'));
        $this->assertSame('F', $arguments->get('key4.key42'));
        $this->assertNull($arguments->get('key5'));

        $this->assertSame('fubar', $arguments->get('key3.yolo', 'fubar'), 'Returns a default value if not found');
    }

    /** @dataProvider data */
    public function test_append($input)
    {
        $arguments = new ExtendedArguments($input);
        $arguments->append('key2.key21', 'C');
        $this->assertEquals(['B', 'C'], $arguments->get('key2.key21'));
    }

    /** @dataProvider data */
    public function test_has($input)
    {
        $arguments = new ExtendedArguments($input);
        $this->assertTrue($arguments->has('key2.key21'));
        $this->assertFalse($arguments->has('key2.key21.fubar'));
        $this->assertTrue($arguments->has('key5'));
    }

    /** @dataProvider data */
    public function test_delete($input)
    {
        $arguments = new ExtendedArguments($input);
        $this->assertEquals('E', $arguments->get('key4.key41.key412'));

        $arguments->delete('key4.key41.key412');
        $this->assertSame('this is deleted', $arguments->get('key4.key41.key412', 'this is deleted'));
    }

    /** @dataProvider data */
    public function test_flatten($input)
    {
        $arguments = new ExtendedArguments($input);
        $expected = [
            'key1' => 'A',
            'key2.key21' => 'B',
            'key3.0' => 'A',
            'key3.1' => 'B',
            'key3.2' => 'C',
            'key4.key41.key411' => 'D',
            'key4.key41.key412' => 'E',
            'key4.key42' => 'F',
            'key5' => null
        ];

        $this->assertEquals(new ExtendedArguments($expected), $arguments->flatten());
    }

    public function test_frakked_up_keys()
    {
        $data = include __DIR__ . '/fixtures/nested-array.php';

        $arguments = new ExtendedArguments($data);
        $this->assertSame($data, $arguments->toArray(), 'The data is intact');

        // But, the keys are messed up now...
        $this->assertSame(true, $arguments->get('array.1'), "The key is TRUE and now juggled into string 1");
        $this->assertSame('null', $arguments->get(''), "The key is NULL but frakked up as empty string");
    }

    /** @dataProvider data */
    public function test_extract($data)
    {
        $arguments = new ExtendedArguments($data);
        $this->assertEquals([
            'key3.1' => 'B',
            'key4.key41.key412' => 'E',
            'key2' => ['key21' => 'B'],
            'non-existent' => null
        ], $arguments->extract(['key3.1', 'key4.key41.key412', 'key2', 'non-existent']));
    }

    public function data()
    {
        return [
            [
                [
                    'key1' => 'A',
                    'key2' => [
                        'key21' => 'B',
                    ],
                    'key3' => ['A', 'B', 'C'],
                    'key4' => [
                        'key41' => [
                            'key411' => 'D',
                            'key412' => 'E',
                        ],
                        'key42' => 'F',
                    ],
                    'key5' => null,
                ]
            ]
        ];
    }
}
