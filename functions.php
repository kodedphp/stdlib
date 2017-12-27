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

use Koded\Stdlib\Interfaces\{ Argument, Data };

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
 * @param string $input The input string
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
