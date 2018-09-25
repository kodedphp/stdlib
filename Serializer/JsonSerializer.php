<?php

namespace Koded\Stdlib\Serializer;

use Koded\Exceptions\KodedException;
use Koded\Stdlib\Interfaces\StringSerializable;

final class JsonSerializer implements StringSerializable
{

    /**
     * @var int JSON encode options. Defaults to (1392):
     *          - JSON_PRESERVE_ZERO_FRACTION
     *          - JSON_NUMERIC_CHECK
     *          - JSON_UNESCAPED_SLASHES
     *          - JSON_UNESCAPED_UNICODE
     *          - JSON_FORCE_OBJECT
     */
    private $options;

    /**
     * JsonSerializer constructor.
     *
     * @param int $options [optional] JSON encode options
     */
    public function __construct(int $options = null)
    {
        $this->options = $options ??
            JSON_PRESERVE_ZERO_FRACTION
            | JSON_NUMERIC_CHECK
            | JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
            | JSON_FORCE_OBJECT;
    }

    public function serialize($value): string
    {
        return json_encode($value, $this->options);
    }

    public function unserialize(string $value)
    {
        $json = json_decode(utf8_encode($value), true, 512, JSON_BIGINT_AS_STRING);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw KodedException::generic(json_last_error_msg());
        }

        return $json;
    }
}
