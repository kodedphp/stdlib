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

class MsgpackSerializer implements Serializer
{
    public function serialize(mixed $value): ?string
    {
        return \msgpack_pack($value);
    }

    public function unserialize(string $value): mixed
    {
        return \msgpack_unpack($value);
    }

    public function type(): string
    {
        return Serializer::MSGPACK;
    }
}
