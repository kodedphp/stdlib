<?php declare(strict_types=1);

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

use RecursiveArrayIterator, RecursiveIteratorIterator;

/**
 * Class ExtendedArguments
 *
 * - NULL key is not supported; also it doesn't make sense
 * - boolean is a wrong type for the key; do not use a boolean key (it's juggled into a string)
 *
 * Use string values for the keys and you'll be golden.
 *
 */
class ExtendedArguments extends Arguments
{

    public function get(string $index, $default = null)
    {
        return $this->find($index, $default);
    }

    public function set(string $index, $value): ExtendedArguments
    {
        $storage = &$this->storage;
        foreach (explode('.', $index) as $i) {
            if (false === is_array($storage[$i]) || false === array_key_exists($i, $storage)) {
                $storage[$i] = [];
            }
            $storage = &$storage[$i];
        }
        $storage = $value;

        return $this;
    }

    public function append($index, $value): ExtendedArguments
    {
        $partial = (array)$this->get($index);
        array_push($partial, $value);
        $this->set($index, $partial);

        return $this;
    }

    public function has($index): bool
    {
        $storage =& $this->storage;
        foreach (explode('.', $index) as $i) {
            if (false === is_array($storage) || false === array_key_exists($i, $storage)) {
                return false;
            }
            $storage =& $storage[$i];
        }

        return true;
    }

    public function delete(string $index): ExtendedArguments
    {
        $storage = &$this->storage;
        foreach (explode('.', $index) as $i) {
            if (false === is_array($storage[$i]) || false === array_key_exists($i, $storage)) {
                continue;
            }
            $storage = &$storage[$i];
        }

        if ($i) {
            unset($storage[$i]);
        }

        return $this;
    }

    public function extract(array $indexes): array
    {
        $found = [];
        foreach ($indexes as $index) {
            $found[$index] = $this->find($index);
        }

        return $found;
    }

    public function flatten(): ExtendedArguments
    {
        $indexes = [];
        $flatten = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($this->storage),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $index => $value) {
            $indexes[$iterator->getDepth()] = $index;

            if (false === is_array($value)) {
                $_ = join('.', array_slice($indexes, 0, $iterator->getDepth() + 1));
                $flatten[$_] = $value;
            }
        }

        return new self($flatten);
    }
}
