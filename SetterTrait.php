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
use Throwable;

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

    public function &pull(string $offset, $default = null)
    {
        if (isset($this->$offset)) {
            $default = $this->$offset;
            $this->offsetUnset($offset);
        }

        return $default;
    }

    public function delete(string $offset): Argument
    {
        try {
            $this->offsetUnset($offset);
        } catch (Throwable $e) {
            // NOOP
        }

        return $this;
    }
}
