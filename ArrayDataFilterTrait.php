<?php

namespace Koded\Stdlib;

trait ArrayDataFilterTrait
{

    public function filter(
        array $data,
        string $prefix,
        bool $lowercase = true,
        bool $trim = true
    ): array
    {
        $filtered = [];

        foreach ($data as $k => $v) {
            if ($trim && '' !== $prefix && 0 === strpos($k, $prefix, 0)) {
                $k = str_replace($prefix, '', $k);
            }
            $filtered[$lowercase ? strtolower($k) : $k] = $v;
        }

        return $filtered;
    }

    public function find(string $index, $default = null)
    {
        $storage = $this->storage;

        if (isset($storage[$index])) {
            return $storage[$index];
        }

        foreach (explode('.', $index) as $token) {
            if (!is_array($storage) || !array_key_exists($token, $storage)) {
                return $default;
            }

            $storage = $storage[$token];
        }

        return $storage;
    }

    public function extract(array $keys): array
    {
        $found = [];
        foreach ($keys as $index) {
            $found[$index] = $this->storage[$index] ?? null;
        }

        return $found;
    }
}