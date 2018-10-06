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

namespace Koded\Stdlib;

use Koded\Stdlib\Interfaces\{Argument, Data};
use Koded\Stdlib\Serializer\{JsonSerializer, PhpSerializer, XmlSerializer};

/**
 * Creates a new Argument instance with optional arbitrary number of arguments.
 *
 * @param array ...$values
 *
 * @return Argument
 */
function arguments(...$values): Argument
{
    return new Arguments(...$values);
}

/**
 * Creates a new Immutable instance with optional arbitrary number of arguments.
 *
 * @param array ...$values
 *
 * @return Data
 */
function value(...$values): Data
{
    return new Immutable(...$values);
}

/**
 * Escapes a string. Useful for escaping the input values in HTML templates.
 *
 * @param string $input    The input string
 * @param string $encoding The encoding
 *
 * @return string
 */
function clean(string $input, string $encoding = 'UTF-8'): string
{
    return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
}

/**
 * Creates a random generated string with optional prefix and/or suffix.
 *
 * NOTE: DO NOT use it for passwords or any data that requires cryptographic secureness!
 *
 * @param int    $length [optional]
 * @param string $prefix [optional]
 * @param string $suffix [optional]
 *
 * @return string
 * @throws \Exception if it was not possible to gather sufficient entropy
 * @since 1.10.0
 */
function random_alpha_numeric(int $length = 16, string $prefix = '', string $suffix = ''): string
{
    $buffer = '';
    for ($x = 0; $x < $length; ++$x) {
        $buffer .= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'[random_int(0, 61)];
    }

    return $prefix . $buffer . $suffix;
}

/**
 * Transforms the simple snake_case string into CamelCaseName.
 *
 * @param string $string
 *
 * @return string Camel-cased string
 */
function snake_to_camel_case(string $string): string
{
    $string = preg_replace('/[\W\_]++/', ' ', $string);

    return str_replace(' ', '', ucwords($string));
}

/**
 * Transforms simple CamelCaseName into camel_case_name (lower case underscored).
 *
 * @param string $string CamelCase string to be underscored
 *
 * @return string Transformed string (for weird strings, you get what you deserve)
 */
function camel_to_snake_case(string $string): string
{
    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', trim($string)));
}

/**
 * Serializes the iterable instance or array into JSON format.
 *
 * @param mixed $data    The data to be serialized, except resource
 * @param int   $options [optional] JSON bitmask options
 *
 * @return string JSON encoded string
 * @see http://php.net/manual/en/function.json-encode.php
 */
function json_serialize($data, int $options = 0): string
{
    return (new JsonSerializer($options))->serialize($data);
}

/**
 * Decodes the encoded JSON string into appropriate PHP type.
 *
 * @param string $json The encoded JSON string
 *
 * @return mixed The value encoded in JSON in appropriate PHP type
 * @throws \Koded\Exceptions\KodedException on error
 */
function json_unserialize(string $json)
{
    return (new JsonSerializer)->unserialize($json);
}

/**
 * Serializes the PHP object into string.
 *
 * @param object $object The PHP object to be serialized
 * @param bool   $binary [optional] TRUE for igbinary serialization,
 *                       or standard PHP serialize() function
 *
 * @return string byte-stream representation of the serialized PHP object
 */
function php_serialize($object, bool $binary = false): string
{
    return (new PhpSerializer($binary))->serialize($object);
}

/**
 * Unserialize the serialized PHP object into it's appropriate type.
 *
 * @param string $serialized The serialized PHP object
 * @param bool   $binary     [optional] TRUE for igbinary serialization,
 *                           or standard PHP serialize() function
 *
 * @return mixed The PHP variant if serialization was successful,
 *               or FALSE if converted object is unserializeable
 */
function php_unserialize(string $serialized, bool $binary = false)
{
    return (new PhpSerializer($binary))->unserialize($serialized);
}

/**
 * Serializes the data into XML document.
 *
 * @param string   $root The XML document root name
 * @param iterable $data The data to be encoded
 *
 * @return string XML document
 */
function xml_serialize(string $root, iterable $data): string
{
    return (new XmlSerializer($root))->serialize($data);

}

/**
 * Unserialize the XML document into PHP array.
 * This function does not deal with magical conversions of complicated XML structures.
 *
 * @param string $root The XML document root name
 * @param string $xml  The XML document to be decoded into array
 *
 * @return array Decoded version of the XML string,
 *               or empty array on malformed XML
 */
function xml_unserialize(string $root, string $xml): array
{
    return (new XmlSerializer($root))->unserialize($xml);
}
