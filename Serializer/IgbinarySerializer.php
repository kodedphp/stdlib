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

use Koded\Stdlib\Interfaces\Serializer;

final class IgbinarySerializer implements Serializer
{

    public function serialize($value)
    {
        return igbinary_serialize($value);
    }

    public function unserialize($value)
    {
        return igbinary_unserialize($value);
    }

    public function type(): string
    {
        return Serializer::IGBINARY;
    }
}
