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

use DateTimeImmutable;
use DateTimeZone;
use Koded\Stdlib\Interfaces\{Argument, Data};
use Koded\Stdlib\Serializer\XmlSerializer;

/**
 * Creates a new Argument instance
 * with optional arbitrary number of arguments.
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
 * Creates a new Immutable instance
 * with optional arbitrary number of arguments.
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
 * Escapes a string.
 * Useful for escaping the input values in HTML templates.
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
    $string = snake_to_camel_case($string);
    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', trim($string)));
}

/**
 * Returns the JSON representation of a value.
 *
 * @param mixed $value   The data to be serialized
 * @param int   $options [optional] JSON bitmask options for JSON encoding
 *
 * @return string JSON encoded string, or EMPTY STRING if encoding failed
 * @see http://php.net/manual/en/function.json-encode.php
 */
function json_serialize($value, int $options = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES): string
{
    if (false === $json = json_encode($value, $options)) {
        error_log(__FUNCTION__, json_last_error_msg(), $value);
        return '';
    }

    return $json;
}

/**
 * Decodes a JSON string into appropriate PHP type.
 *
 * @param string $json A JSON string
 *
 * @return mixed The decoded value, or EMPTY STRING on error
 */
function json_unserialize(string $json)
{
    $data = json_decode($json, false, 512, JSON_BIGINT_AS_STRING);

    if (JSON_ERROR_NONE !== json_last_error()) {
        error_log(__FUNCTION__, json_last_error_msg(), $json);
        return '';
    }

    return $data;
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
 * Unserialize an XML document into PHP array.
 * This function does not deal with magical conversions
 * of complicated XML structures.
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

/**
 * Send a formatted error message to PHP's system logger.
 *
 * @param string $function The function name where error occurred
 * @param string $message  The error message
 * @param mixed  $data     Original data passed into function
 */
function error_log(string $function, string $message, $data): void
{
    \error_log(sprintf('(%s) [Error] - %s - data: %s',
        $function, $message, var_export($data, true)
    ));
}

/**
 * Checks if the array is an associative array.
 *
 * Simple rules:
 *
 * - If all keys are sequential starting from 0..n, it is not an associative array
 * - empty array is not associative
 *
 * Unfortunately, the internal typecast to integer on the keys makes
 * the sane programming an ugly PHP Array Oriented Programming hackery.
 *
 * @param array $array
 *
 * @return bool
 */
function is_associative(array $array): bool
{
    return (bool)array_diff_assoc($array, array_values($array));
}

/**
 * Gets an instance of DateTimeImmutable in UTC.
 *
 * @return DateTimeImmutable
 */
function now(): DateTimeImmutable
{
    return date_create_immutable('now', timezone_open('UTC'));
}