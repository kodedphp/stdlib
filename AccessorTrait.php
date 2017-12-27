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

    public function & __get($index)
    {
        if (false === array_key_exists($index, $this->storage)) {
            $this->storage[$index] = null;
        }

        return $this->storage[$index];
    }

    public function __set($index, $value)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
    }

    public function __clone()
    {
        throw new ReadOnlyException(Data::E_CLONING_DISALLOWED, [':class' => get_class($this)]);
    }

    public function __isset($index)
    {
        return $this->has($index);
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

    public function toJSON(int $options = 0): string
    {
        $options |= JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES;
        return json_encode($this, $options);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->storage as $k => $v) {
            yield $k => $v;
        }
    }

    public function jsonSerialize(): array
    {
        return $this->storage;
    }
}
