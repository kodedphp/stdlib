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

/**
 * Dump the variable with extra info.
 *
 * @param mixed $var
 * @param string|null $label [optional]
 * @param bool $traceback [optional]
 * @param bool $die [optional]
 */
function dump($var, $label = null, bool $traceback = false, bool $die = true)
{
    $MESSAGE_CLI = "\n>>> %s[type] %s\n[info] %s\n[line] \x1b[1m%s\x1b[0m\n\x1b[2m%s\x1b[0m";
    $MESSAGE_HTM = '<span style="clear:both; color:black; font-size:11px;">[<b>%s</b>] %s (%s) %s</span>';

    list($position, $backtrace) = (function(array $backtrace) {
        if (isset($backtrace[1]) && '__invoke' === $backtrace[1]['function']) {
            $p = $backtrace[1]['class'] . $backtrace[1]['type'] . $backtrace[1]['function'] . ':' . $backtrace[0]['line'];
        } else if ('dump' === $backtrace[0]['function'] && count($backtrace) > 1) {
            $p = $backtrace[1]['class'] ?? $backtrace[0]['file'] ?? __FILE__;
            $p .= $backtrace[1]['type'] ?? ' *';
            $p .= $backtrace[1]['function'] . ':' . $backtrace[0]['line'] ?? $backtrace[1]['line'];
        } else {
            $p = $backtrace[0]['class'] ?? $backtrace[0]['file'];
            $p .= $backtrace[0]['type'] ?? ':' . $backtrace[0]['line'];
            $p .= ' ' . $backtrace[0]['function'] . '()';
        }

        // backtrace
        $b = array_map(function($l) {
            $MESSAGE_BACKTRACE = "  File \"\x1b[36;2m%s\x1b[0m\", line \x1b[36;2m%d\x1b[0m\n    from %s\x1b[1m%s()\x1b[0m";
            return sprintf($MESSAGE_BACKTRACE, $l['file'] ?? '?', $l['line'] ?? -1, (($l['class'] ?? '') ? $l['class'] . '::' : ''), $l['function']);
        }, array_slice(array_reverse($backtrace), 0, -1));

        return [$p, join(PHP_EOL, $b)];

    })(debug_backtrace());

    $backtrace = ($traceback ? 'Traceback (most recent call last):' . PHP_EOL . $backtrace . PHP_EOL . str_repeat('-', 80) : date(DATE_COOKIE)) . PHP_EOL;

    if ('cli' === php_sapi_name()) {
        $output = sprintf($MESSAGE_CLI, $backtrace, gettype($var), var_export($label, true), $position, print_r($var, true));
    } elseif (strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'XMLHTTPREQUEST') {
        $output = json_encode($var, JSON_UNESCAPED_SLASHES);
    } else {
        $format = function($var) {
            ob_start();
            var_dump($var);
            $dump = ob_get_contents();
            $dump = preg_replace('/<small>\/.*<\/small>/', '\\1', $dump);
            ob_end_clean();

            return $dump;
        };

        $output = sprintf($MESSAGE_HTM, $position, gettype($var), var_export($label, true), $format($var));
    }

    $die and die($output);
    print $output;
}
