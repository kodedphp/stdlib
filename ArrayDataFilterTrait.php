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

trait ArrayDataFilterTrait
{
    public function filter(
        iterable $data,
        string $prefix,
        bool $lowercase = true,
        bool $trim = true
    ): array {
        $filtered = [];

        foreach ($data as $index => $value) {
            if ($trim && '' !== $prefix && 0 === strpos($index, $prefix, 0)) {
                $index = str_replace($prefix, '', $index);
            }
            $filtered[$lowercase ? strtolower($index) : $index] = $value;
        }
        return $filtered;
    }

    public function find(string $index, $default = null)
    {
        if (isset($this->storage[$index])) {
            return $this->storage[$index];
        }

        $storage = $this->storage;
        foreach (explode('.', $index) as $token) {
            if (false === is_array($storage) || false === array_key_exists($token, $storage)) {
                return $default;
            }

            $storage = &$storage[$token];
        }
        return $storage;
    }

    public function extract(array $indexes): array
    {
        $found = [];
        foreach ($indexes as $index) {
            $found[$index] = $this->storage[$index] ?? null;
        }
        return $found;
    }
}
