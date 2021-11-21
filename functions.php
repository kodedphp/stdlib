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
use Exception;
use FilesystemIterator;
use Koded\Stdlib\Serializer\{JsonSerializer, XmlSerializer};
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function array_diff_assoc;
use function array_key_exists;
use function array_product;
use function array_values;
use function chr;
use function date_create_immutable;
use function getenv;
use function htmlentities;
use function join;
use function ord;
use function preg_replace;
use function preg_split;
use function putenv;
use function random_int;
use function sprintf;
use function str_replace;
use function strtolower;
use function timezone_open;
use function trim;
use function ucwords;
use function unlink;
use function var_export;

/**
 * Creates a new Arguments instance
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
 * Send a formatted error message to PHP's system logger.
 *
 * @param string $func The function name where error occurred
 * @param string $message The error message
 * @param mixed $data Original data passed into function
 */
function error_log(string $func, string $message, mixed $data): void
{
    \error_log(sprintf("(%s) %s:\n%s", $func, $message, var_export($data, true)));
}

/**
 * Gets or sets environment variables.
 *
 * @param string|null $name
 * @param string [optional] $name The name of the env variable
 * @param array|null $initialState
 * @return mixed The value for the env variable,
 *               or all variables if $name is not provided
 */
function env(
    string $name = null,
    mixed  $default = null,
    array  $initialState = null): mixed
{
    static $state = [];
    if (null !== $initialState) {
        foreach ($initialState as $k => $v) {
            null === $v || putenv($k . '=' . $v);
        }
        return $state = $initialState;
    }
    if (null === $name) {
        return $state;
    }
    return array_key_exists($name, $state)
        ? $state[$name]
        : (getenv($name) ?: $default);
}

/**
 * HTML encodes a string.
 * Useful for escaping the input values in HTML templates.
 *
 * @param string $input The input string
 * @param string $encoding The encoding
 *
 * @return string
 */
function htmlencode(string $input, string $encoding = 'UTF-8'): string
{
    return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
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
 * Returns the JSON representation of a value.
 *
 * @param mixed $value The data to be serialized
 * @param int $options [optional] JSON bitmask options for JSON encoding.
 *                       [WARNING]: uses {@JsonSerializer::OPTIONS} as defaults;
 *                       instead of adding, it may remove the option (if set in OPTIONS)
 *
 * @return string JSON encoded string, or EMPTY STRING if encoding failed
 * @see http://php.net/manual/en/function.json-encode.php
 */
function json_serialize(mixed $value, int $options = 0): string
{
    return (new JsonSerializer($options))->serialize($value);
}

/**
 * Decodes a JSON string into appropriate PHP type.
 *
 * @param string $json A JSON string
 * @param bool $associative When TRUE, returned objects will be
 *                            converted into associative arrays
 *
 * @return mixed The decoded value, or EMPTY STRING on error
 */
function json_unserialize(string $json, bool $associative = false): mixed
{
    return (new JsonSerializer(0, $associative))->unserialize($json);
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
 * Creates a random generated string with optional prefix and/or suffix.
 *
 * NOTE: DO NOT use it for passwords or any data that requires cryptographic secureness!
 *
 * @param int $length [optional]
 * @param string $prefix [optional]
 * @param string $suffix [optional]
 *
 * @return string
 * @throws Exception if it was not possible to gather sufficient entropy
 * @since 1.10.0
 */
function randomstring(
    int    $length = 16,
    string $prefix = '',
    string $suffix = ''): string
{
    $buffer = '';
    for ($x = 0; $x < $length; ++$x) {
        $buffer .= '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'[random_int(0, 61)];
    }
    return $prefix . $buffer . $suffix;
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
        $deleted[] = ($path->isDir() && false === $path->isLink())
            ? \rmdir($path->getPathname())
            : unlink($path->getPathname());
    }
    return (bool)array_product($deleted);
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
    $string = preg_replace('/[\W_]++/', ' ', $string);
    return str_replace(' ', '', ucwords($string));
}

/**
 * Converts the string with desired delimiter character.
 *
 * @param string $string
 * @param int $delimiter chr() of the delimiter character
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
 * Creates a new Data instance (Immutable)
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
 * Serializes the data into XML document.
 *
 * @param string $root The XML document root name
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
