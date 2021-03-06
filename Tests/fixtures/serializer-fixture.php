<?php

return [
    '@defaultNormalizers' => 'true',
    '@runtime'            => 'debug',
    'generator'           => [
        'namespace' => 'Generated\\Class',
        'directory' => './build/generators',
    ],
    'bindings'            => [
        'class' => [
            [
                '@name' => 'Koded\\App\\Some\\AbstractClass',
                'bind'  => [
                    [
                        '@property' => 'foo',
                        '@type'     => 'bool',
                        '#'         => 'Koded\\App\\Concrete\\Implementation\\Foo',
                    ],
                    [
                        '@property' => 'bar',
                        '@type'     => 'class',
                        '#'         => 'Koded\\App\\Concrete\\Implementation\\Bar',
                    ],
                ],
            ],
            [
                '@name' => 'Koded\\App\\Some\\Interface',
                'bind'  => [
                    [
                        '@property' => 'qux',
                        '#'         => 'Koded\\App\\Concrete\\Implementation\\Qux',
                    ],
                    [
                        '@property' => 'zim',
                        '@type'     => 'int',
                        '#'         => 'Koded\\App\\Concrete\\Implementation\\Zim',
                    ],
                ],
            ],
        ],
    ],
    'normalizer'          => [
        [
            '@class' => 'Koded\\Serializer\\Normalizer\\ObjectNormalizer',
            '#' => '',
        ],
        [
            '@class' => 'Koded\\Serializer\\Normalizer\\CollectionNormalizer',
            '#' => '',
        ],
        [
            '@class'   => 'Koded\\Serializer\\Normalizer\\DateTimeNormalizer',
            'argument' => [
                [
                    '@name' => 'format',
                    '#'     => 'd/m/Y H:i:s',
                ],
                [
                    '@name' => 'timezone',
                    '#'     => 'UTC',
                ],
            ],
        ],
    ],
    'caching'             => [
        '@client'     => 'file',
        '@ttl'        => '1200',
        '@serializer' => 'php',
        'arguments'   => [
            'dir' => '/tmp/koded/serializer',
            'ttl' => '3000',
        ],
    ],
];