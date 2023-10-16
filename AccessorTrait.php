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

use Koded\Exceptions\ReadOnlyException;
use Traversable;
use function array_key_exists;
use function count;
use function get_class;

/**
 * @property array $data
 */
trait AccessorTrait
{
    public function &__get($index)
    {
        if (false === array_key_exists($index, $this->data)) {
            $this->data[$index] = null;
        }
        return $this->data[$index];
    }

    public function __set($index, $value)
    {
        throw ReadOnlyException::forInstance($index, get_class($this));
    }

    public function __clone()
    {
        throw ReadOnlyException::forCloning(get_class($this));
    }

    public function __isset($index)
    {
        return $this->has($index);
    }

    public function get(string $index, mixed $default = null): mixed
    {
        return $this->data[$index] ?? $default;
    }

    public function has(string $index): bool
    {
        return array_key_exists($index, $this->data);
    }

    public function equals(string $propertyA, string $propertyB): bool
    {
        return $this->get($propertyA) === $this->get($propertyB);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toJSON(int $options = 0): string
    {
        return json_serialize($this->data, $options);
    }

    public function toXML(string $root): string
    {
        return xml_serialize($root, $this->data);
    }

    public function getIterator(): Traversable
    {
        foreach ($this->data as $k => $v) {
            yield $k => $v;
        }
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}
