<?php

namespace Koded\Stdlib\Serializer;

use Koded\Stdlib\Interfaces\StringSerializable;

final class PhpSerializer implements StringSerializable
{

    private $binary;

    public function __construct(bool $binary)
    {
        $this->binary = $binary && function_exists('igbinary_serialize');
    }

    public function serialize($value): string
    {
        return $this->binary ? igbinary_serialize($value) : serialize($value);
    }

    public function unserialize(string $value)
    {
        return $this->binary ? igbinary_unserialize($value) : unserialize($value);
    }
}
