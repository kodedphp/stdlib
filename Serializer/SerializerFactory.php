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
use Koded\Stdlib\Interfaces\StringSerializable;

final class SerializerFactory
{
    const IGBINARY = 'igbinary';
    const MSGPACK = 'msgpack';
    const JSON = 'json';
    const XML = 'xml';
    const PHP = 'php';

    /**
     * Factory that creates a new instance of StringSerializable.
     *
     * @param string $name The name of the supported serializer
     *                     Provide a FQCN for custom serializers
     * @param        $args [optional] Optional arguments for the serializer class
     *
     * @return StringSerializable
     * @throws SerializerException
     */
    public static function new(string $name, $args = null): StringSerializable
    {
        switch ($name) {
            case self::JSON:
                return new JsonSerializer((int)$args);

            case self::IGBINARY:
                // @codeCoverageIgnoreStart
                if (false === function_exists('igbinary_serialize')) {
                    throw SerializerException::forMissingModule(SerializerFactory::MSGPACK);
                }

                return new IgbinarySerializer;
            // @codeCoverageIgnoreEnd

            case self::PHP:
                return new PhpSerializer;

            case self::XML:
                return new XmlSerializer((string)$args);

            case self::MSGPACK:
                // @codeCoverageIgnoreStart
                if (false === function_exists('msgpack_pack')) {
                    throw SerializerException::forMissingModule(SerializerFactory::MSGPACK);
                }

                return new MsgpackSerializer;
            // @codeCoverageIgnoreEnd
        }

        if (is_a($name, StringSerializable::class, true)) {
            return new $name($args);
        }

        throw SerializerException::forCreate($name);
    }
}
