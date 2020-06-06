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

    public function serialize($value)
    {
        return json_serialize($value, $this->options);
    }

    public function unserialize($value)
    {
        return json_unserialize($value);
    }

    public function type(): string
    {
        return Serializer::JSON;
    }
}
