<?php

$datetime = new \DateTime('2167-04-30T09:16:15+0000');

$stdClass = new \stdClass;
$stdClass->key = 'value';

return [
    'message' => 'The error message & it\'s "details"',
    'code' => 400,
    'errors' => [
        'field-one' => [
            'explain' => 'Field one',
            'reason' => 'The Reason'
        ],
        'field-two' => 'Field two'
    ],
    'arguments' => [
        'it',
        'equals',
        42
    ],
    'datetime' => $datetime,
    'nothing' => null,
    'instance' => $stdClass,
];