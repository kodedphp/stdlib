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

/**
 * @property array $storage
 */
trait MutatorTrait
{

    public function __set($index, $value)
    {
        return $this->set($index, $value);
    }

    public function set(string $index, $value): self
    {
        $this->storage[$index] = $value;

        return $this;
    }

    public function bind(string $index, &$variable): self
    {
        $this->storage[$index] = &$variable;

        return $this;
    }

    public function upsert(string $index, $value)
    {
        return $this->has($index) ? $this : $this->set($index, $value);
    }

    public function pull(string $index, $default = null)
    {
        $value = $this->get($index, $default);
        unset($this->storage[$index]);

        return $value;
    }

    public function import(array $array): self
    {
        foreach ($array as $index => $value) {
            $this->storage[$index] = $value;
        }

        return $this;
    }

    public function delete(string $index): self
    {
        unset($this->storage[$index]);

        return $this;
    }

    public function clear(): self
    {
        $this->storage = [];

        return $this;
    }
}
