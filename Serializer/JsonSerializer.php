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

use JsonException;
use Koded\Stdlib\Serializer;
use function Koded\Stdlib\error_log;

class JsonSerializer implements Serializer
{
    public const OPTIONS = JSON_PRESERVE_ZERO_FRACTION
    | JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_THROW_ON_ERROR;

    /**
     * @var int JSON encode options. Defaults to (4195648):
     *          - JSON_PRESERVE_ZERO_FRACTION
     *          - JSON_UNESCAPED_SLASHES
     *          - JSON_UNESCAPED_UNICODE
     *          - JSON_THROW_ON_ERROR
     */
    private int $options = self::OPTIONS;

    private bool $associative;

    /**
     * JsonSerializer constructor.
     *
     * @param int $options [optional] JSON encode options.
     *                          - to ADD more JSON options use OR "|" bitmask operator
     *                          - to EXCLUDE multiple default options use XOR "^"
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
        try {
            return json_encode($value, $this->options);
        } catch (JsonException $e) {
            error_log(__METHOD__, $e->getMessage(), $value);
            return '';
        }

    }

    public function unserialize(string $value): mixed
    {
        try {
            return json_decode($value, $this->associative, 512,
                JSON_OBJECT_AS_ARRAY
                | JSON_BIGINT_AS_STRING
                | JSON_THROW_ON_ERROR);

        } catch (JsonException $e) {
            error_log(__METHOD__, $e->getMessage(), $value);
            return '';
        }
    }

    public function type(): string
    {
        return Serializer::JSON;
    }
}
