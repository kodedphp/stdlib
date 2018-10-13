<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 *
 */

namespace Koded\Stdlib\Serializer;

use Koded\Exceptions\KodedException;
use Koded\Stdlib\Interfaces\Serializer;

final class JsonSerializer implements Serializer
{

    /**
     * @var int JSON encode options. Defaults to (1088):
     *          - JSON_PRESERVE_ZERO_FRACTION
     *          - JSON_UNESCAPED_SLASHES
     */
    private $options = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES;

    /**
     * JsonSerializer constructor.
     *
     * @param int $options [optional] JSON encode options.
     *                     - to add more JSON options use OR "|" bitmask operator
     *                     - to exclude multiple default options use XOR "^"
     */
    public function __construct(int $options = 0)
    {
        $this->options ^= $options;
    }

    public function serialize($value): string
    {
        return json_encode($value, $this->options);
    }

    public function unserialize(string $value)
    {
        $json = json_decode($value, false, 512, JSON_BIGINT_AS_STRING);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw KodedException::generic(json_last_error_msg());
        }

        return $json;
    }

    public function name(): string
    {
        return Serializer::JSON;
    }
}
