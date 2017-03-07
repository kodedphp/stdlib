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

use Koded\Stdlib\Interfaces\Argument;

/**
 * SetterTrait
 *
 */
trait SetterTrait
{

    public function import(array $array): Argument
    {
        foreach ($array as $offset => $value) {
            $this->$offset = $value;
        }

        return $this;
    }

    public function upsert(string $offset, $value): Argument
    {
        return isset($this->$offset) ? $this : $this->set($offset, $value);
    }

    public function set(string $offset, $value): Argument
    {
        $this->$offset = $value;

        return $this;
    }

    public function bind(string $offset, &$variable): Argument
    {
        $this->$offset = &$variable;

        return $this;
    }

    public function pull(string $offset, $default = null)
    {
        $value = $this->get($offset, $default);
        unset($this[$offset]);

        return $value;
    }

    public function delete(string $offset): Argument
    {
        return $this->offsetUnset($offset);
    }

    /**
     * @internal
     * {@inheritdoc}
     * @return $this
     */

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            parent::offsetUnset($offset);
        }

        return $this;
    }
}
