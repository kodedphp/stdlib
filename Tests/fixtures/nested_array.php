<?php

return [
    'foo' => 'bar',
    '0' => 0,
    null => 'null',
    'null' => null,
    'list' => [
        1, 2, 3
    ],
    'array' => [
        'key1' => 1,
        'key2' => [
            'key2.1' => null
        ],
        'key3' => [
            'key3-1' => [
                'key3-1-1' => 'found me'
            ],

        ],
        'key4.0' => 'nested keys cannot have dots',
        true => true,
    ],
    'one.two.three' => 'four',
    true => true,
];