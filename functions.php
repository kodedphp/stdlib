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

use Koded\Stdlib\Interfaces\{ Arguments, Data };

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
