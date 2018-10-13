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

use Koded\Stdlib\Interfaces\StringSerializable;

final class IgbinarySerializer implements StringSerializable
{

    public function serialize($value): string
    {
        return igbinary_serialize($value);
    }

    public function unserialize(string $value)
    {
        return igbinary_unserialize($value);
    }

    public function name(): string
    {
        return SerializerFactory::IGBINARY;
    }
}
