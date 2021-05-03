<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 */

namespace Koded\Stdlib\Serializer;

use Koded\Stdlib\Serializer;
use function Koded\Stdlib\{json_serialize, json_unserialize};

class JsonSerializer implements Serializer
{
    public const OPTIONS = JSON_PRESERVE_ZERO_FRACTION
    | JSON_UNESCAPED_SLASHES
    | JSON_THROW_ON_ERROR;

    /**
     * @var int JSON encode options. Defaults to (1088):
     *          - JSON_PRESERVE_ZERO_FRACTION
     *          - JSON_UNESCAPED_SLASHES
     */
    private int $options = self::OPTIONS;

    private bool $associative;

    /**
     * JsonSerializer constructor.
     *
     * @param int  $options     [optional] JSON encode options.
     *                          - to add more JSON options use OR "|" bitmask operator
     *                          - to exclude multiple default options use XOR "^"
     * @param bool $associative [optional] When TRUE, returned objects will be
     *                          converted into associative arrays
     */
    public function __construct(int $options = 0, bool $associative = false)
    {
        $this->options ^= $options;
        $this->associative = $associative;
    }

    public function serialize(mixed $value): ?string
    {
        return json_serialize($value, $this->options);
    }

    public function unserialize(string $value): mixed
    {
        return json_unserialize($value, $this->associative);
    }

    public function type(): string
    {
        return Serializer::JSON;
    }
}
