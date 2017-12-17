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

use IteratorAggregate, Countable;
use Koded\Stdlib\Interfaces\{ Argument, NamespaceDataFilter, TransformsToImmutable };

/**
 * Arguments is a MUTABLE (multi purpose) class that encapsulates data.
 *
 * TIP: Avoid creating a child classes with properties from this one.
 * It will mess up your Zen.
 */
class Arguments implements Argument, TransformsToImmutable, NamespaceDataFilter, IteratorAggregate, Countable
{

    use AccessorTrait, MutatorTrait, ArrayDataFilterTrait {
        MutatorTrait::__set insteadof AccessorTrait;
    }

    protected $storage = [];

    public function __construct(array $values = [])
    {
        $this->storage = $values;
    }

    public function __clone()
    {
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

    public function namespace(string $prefix, bool $lowercase = true, bool $trim = true)
    {
        return new static($this->filter($this->toArray(), $prefix, $lowercase, $trim));
    }

    public function toImmutable(): Immutable
    {
        return new Immutable($this->storage);
    }
}
