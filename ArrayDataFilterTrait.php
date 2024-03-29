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

use function array_key_exists;
use function explode;
use function str_replace;
use function str_starts_with;
use function strtolower;

trait ArrayDataFilterTrait
{
    public function filter(
        iterable $data,
        string $prefix,
        bool $lowercase = true,
        bool $trim = true): array
    {
        $filtered = [];
        foreach ($data as $index => $value) {
            if ($trim && '' !== $prefix && str_starts_with($index, $prefix)) {
                $index = str_replace($prefix, '', $index);
            }
            $filtered[$lowercase ? strtolower($index) : $index] = $value;
        }
        return $filtered;
    }

    public function find(string $index, mixed $default = null): mixed
    {
        if (isset($this->data[$index])) {
            return $this->data[$index];
        }
        $data = $this->data;
        foreach (explode('.', $index) as $token) {
            if (false === array_key_exists($token, $data)) {
                return $default;
            }
            $data =& $data[$token];
        }
        return $data;
    }

    public function extract(array $indexes): array
    {
        $found = [];
        foreach ($indexes as $index) {
            $found[$index] = $this->data[$index] ?? null;
        }
        return $found;
    }
}
