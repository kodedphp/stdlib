<?php

$datetime = new \DateTimeImmutable('2167-04-30T09:16:15+0000');

$stdClass = new \stdClass;
$stdClass->key = 'val';

return [
    'message'  => 'The error message & it\'s "details"',
    'code'     => 400,
    'error'    => [
        'field-one' => [
            'explain' => 'Field one',
            'reason'  => 'The <b>Reason</b>'
        ],
        'field-two' => 'Field two/2',
        'de'        => 'Straße'
    ],
    'argument' => [
        'it',
        'equals',
        42.0
    ],
    'extras'   => [
        'foo',
        'bar',
        'bar' => [1, 2, 3]
    ],
    'item'     => [
        ['title' => 'lorem'],
        ['title' => 'ipsum'],
        ['title' => 'dolor'],
    ],
    'handled'  => true,
    'nothing'  => null,
    'empty'    => '',
    'datetime' => $datetime,
    'object'   => $stdClass,
];