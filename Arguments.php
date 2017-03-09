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

use Koded\Stdlib\Interfaces\{ Argument, Data };

/**
 * Arguments is a MUTABLE (multi purpose) class that encapsulates data.
 *
 * TIP: Avoid creating a child classes with properties from this one.
 * It will mess up your Zen.
 */
class Arguments extends Immutable implements Argument
{

    public function __set($index, $value): Argument
    {
        return $this->set($index, $value);
    }

    public function __clone()
    {
    }

    public function set(string $index, $value): Argument
    {
        $this->storage[$index] = $value;

        return $this;
    }

    public function upsert(string $index, $value): Argument
    {
        return $this->has($index) ? $this : $this->set($index, $value);
    }

    public function bind(string $index, &$variable): Argument
    {
        $this->storage[$index] = &$variable;

        return $this;
    }

    public function pull(string $index, $default = null)
    {
        $value = $this->get($index, $default);
        unset($this->storage[$index]);

        return $value;
    }

    public function import(array $array): Argument
    {
        foreach ($array as $index => $value) {
            $this->storage[$index] = $value;
        }

        return $this;
    }

    public function delete(string $index): Argument
    {
        unset($this->storage[$index]);

        return $this;
    }

    /**
     * @experimental
     *
     * @return Data
     */
    public function toImmutable(): Data
    {
        return new Immutable($this->toArray());
    }
}