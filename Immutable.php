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

use Countable;
use IteratorAggregate;
use Koded\Exceptions\ReadOnlyException;
use Koded\Stdlib\Interfaces\{
    Argument, Data
};
use Traversable;

/**
 * An IMMUTABLE multi purpose class that encapsulates a read-only data.
 * It is useful for passing it around as a DTO.
 */
class Immutable implements IteratorAggregate, Countable, Data
{

    /**
     * @var array The internal data storage
     */
    protected $storage = [];

    /**
     * Sets the object store with values.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->storage = $values;
    }

    public function __clone()
    {
        throw new ReadOnlyException(Data::E_CLONING_DISALLOWED, [':class' => get_class($this)]);
    }

    public function __get($index)
    {
        return $this->get($index);
    }

    public function __set($index, $value)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
    }

    public function get(string $index, $default = null)
    {
        return $this->storage[$index] ?? $default;
    }

    public function find(string $index, $default = null)
    {
        $array = $this->toArray();

        if (isset($array[$index])) {
            return $array[$index];
        }

        foreach (explode('.', $index) as $token) {
            if (!is_array($array) or !array_key_exists($token, $array)) {
                return $default;
            }

            $array = $array[$token];
        }

        return $array;
    }

    public function toArray(): array
    {
        return $this->storage;
    }

    public function has($index): bool
    {
        return array_key_exists($index, $this->storage);
    }

    public function extract(array $keys): Data
    {
        $array = [];

        foreach ($keys as $index) {
            if (isset($this->storage[$index]) or array_key_exists($index, $this->storage)) {
                $array[$index] = $this->storage[$index];
            }
        }

        return new static($array);
    }

    public function count()
    {
        return count($this->storage);
    }

    /**
     * @experimental
     *
     * @return Argument
     */
    public function toArgument(): Argument
    {
        return new Arguments($this->toArray());
    }

    /**
     * @internal
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        foreach ($this->storage as $k => $v) {
            yield $k => $v;
        }
    }
}
