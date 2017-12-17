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

use Koded\Exceptions\ReadOnlyException;
use Koded\Stdlib\Interfaces\Data;
use Traversable;

trait AccessorTrait
{

    public function __get($index)
    {
        return $this->get($index);
    }

    public function __set($index, $value)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
    }

    public function __clone()
    {
        throw new ReadOnlyException(Data::E_CLONING_DISALLOWED, [':class' => get_class($this)]);
    }

    public function get(string $index, $default = null)
    {
        return $this->storage[$index] ?? $default;
    }

    public function toArray(): array
    {
        return $this->storage;
    }

    public function has($index): bool
    {
        return array_key_exists($index, $this->storage);
    }

    public function count()
    {
        return count($this->storage);
    }

    /**
     * @internal
     *
     * Retrieve an external iterator.
     *
     * @return Traversable An instance of an object implementing Iterator or Traversable
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     */
    public function getIterator(): Traversable
    {
        foreach ($this->storage as $k => $v) {
            yield $k => $v;
        }
    }
}
