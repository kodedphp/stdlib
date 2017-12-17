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
