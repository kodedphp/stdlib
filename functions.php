<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
*/

namespace Koded\Stdlib;

use DateTimeImmutable;
use FilesystemIterator;
use JsonException;
use Koded\Stdlib\Serializer\{JsonSerializer, XmlSerializer};
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
 * HTML encodes a string.
 * Useful for escaping the input values in HTML templates.
 *
 * @param string $input    The input string
 * @param string $encoding The encoding
 *
 * @return string
 */
function htmlencode(string $input, string $encoding = 'UTF-8'): string
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
function randomstring(int $length = 16, string $prefix = '', string $suffix = ''): string
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
 * Converts the string with desired delimiter character.
 *
 * @param string $string
 * @param int    $delimiter chr() of the delimiter character
 *
 * @return string The converted string with the provided delimiter
 */
function to_delimited_string(string $string, int $delimiter): string
{
    $str = preg_split('~[^\p{L}\p{N}\']+~u', trim($string));
    return join(chr($delimiter), $str);
}

/**
 * Converts the string to-kebab-case
 *
 * @param string $string
 *
 * @return string
 */
function to_kebab_string(string $string): string
{
    return strtolower(to_delimited_string($string, ord('-')));
}

/**
 * Returns the JSON representation of a value.
 *
 * @param mixed $value   The data to be serialized
 * @param int   $options [optional] JSON bitmask options for JSON encoding.
 *                       Warning: uses {@JsonSerializer::OPTIONS} as defaults;
 *                       instead of adding, it may remove the option (if set in OPTIONS)
 *
 * @return string JSON encoded string, or EMPTY STRING if encoding failed
 * @see http://php.net/manual/en/function.json-encode.php
 */
function json_serialize($value, int $options = JsonSerializer::OPTIONS): string
{
    try {
        return json_encode($value, $options);
    } catch (JsonException $e) {
        error_log(__FUNCTION__, $e->getMessage(), $value);
        return '';
    }
}

/**
 * Decodes a JSON string into appropriate PHP type.
 *
 * @param string $json        A JSON string
 * @param bool   $associative When TRUE, returned objects will be
 *                            converted into associative arrays
 *
 * @return mixed The decoded value, or EMPTY STRING on error
 */
function json_unserialize(string $json, bool $associative = false)
{
    try {
        return json_decode($json, $associative, 512, JSON_OBJECT_AS_ARRAY | JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        error_log(__FUNCTION__, $e->getMessage(), $json);
        return '';
    }
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
 * @param string $xml The XML document to be decoded into array
 *
 * @return array Decoded version of the XML string,
 *               or empty array on malformed XML
 */
function xml_unserialize(string $xml): array
{
    return (new XmlSerializer(null))->unserialize($xml) ?: [];
}

/**
 * Send a formatted error message to PHP's system logger.
 *
 * @param string $func    The function name where error occurred
 * @param string $message The error message
 * @param mixed  $data    Original data passed into function
 */
function error_log(string $func, string $message, $data): void
{
    \error_log(sprintf("(%s) %s:\n%s", $func, $message, var_export($data, true)));
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
 * the sane programming an ugly Array Oriented Programming hackery.
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

/**
 * Removes a directory.
 *
 * @param string $dirname The folder name
 *
 * @return bool TRUE on success, FALSE otherwise
 */
function rmdir(string $dirname): bool
{
    $deleted = [];

    /** @var \SplFileInfo $path */
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirname, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST) as $path) {
        $deleted[] = ($path->isDir() && false === $path->isLink()) ? \rmdir($path->getPathname()) : \unlink($path->getPathname());
    }

    return (bool)array_product($deleted);
}
