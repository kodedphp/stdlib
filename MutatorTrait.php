<?php declare(strict_types=1);

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
 * @property array $storage
 */
trait MutatorTrait
{
    public function __set($index, $value)
    {
        return $this->set($index, $value);
    }

    public function set(mixed $index, mixed $value): static
    {
        $this->storage[$index] = $value;
        return $this;
    }

    public function bind(string $index, mixed &$variable): static
    {
        $this->storage[$index] = &$variable;
        return $this;
    }

    public function upsert(string $index, mixed $value): static
    {
        return $this->has($index) ? $this : $this->set($index, $value);
    }

    public function pull(string $index, mixed $default = null): mixed
    {
        $value = $this->get($index, $default);
        unset($this->storage[$index]);
        return $value;
    }

    public function import(array $array): static
    {
        foreach ($array as $index => $value) {
            $this->storage[$index] = $value;
        }
        return $this;
    }

    public function delete(string $index): static
    {
        unset($this->storage[$index]);
        return $this;
    }

    public function clear(): static
    {
        $this->storage = [];
        return $this;
    }
}
