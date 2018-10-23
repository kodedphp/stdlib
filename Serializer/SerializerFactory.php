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

use Koded\Exceptions\SerializerException;
use Koded\Stdlib\Interfaces\Serializer;

final class SerializerFactory
{

    /**
     * Factory that creates a new instance of Serializer.
     *
     * @param string $name The name of the supported serializer
     *                     Provide a FQCN for custom serializers
     * @param        $args [optional] Optional arguments for the serializer class
     *
     * @return Serializer
     * @throws SerializerException
     */
    public static function new(string $name, $args = null): Serializer
    {
        switch ($name) {
            case Serializer::JSON:
                return new JsonSerializer((int)$args);

            case Serializer::PHP:
                return new PhpSerializer;

            case Serializer::XML:
                return new XmlSerializer((string)$args);

            case Serializer::IGBINARY:
                // @codeCoverageIgnoreStart
                if (false === extension_loaded('igbinary')) {
                    throw SerializerException::forMissingModule(Serializer::MSGPACK);
                }

                return new IgbinarySerializer;
            // @codeCoverageIgnoreEnd

            case Serializer::MSGPACK:
                // @codeCoverageIgnoreStart
                if (false === extension_loaded('msgpack')) {
                    throw SerializerException::forMissingModule(Serializer::MSGPACK);
                }

                return new MsgpackSerializer;
            // @codeCoverageIgnoreEnd
        }

        if (is_a($name, Serializer::class, true)) {
            return new $name($args);
        }

        throw SerializerException::forCreateSerializer($name);
    }
}
