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

use ArrayObject;
use Koded\Exceptions\ReadOnlyException;
use Koded\Stdlib\Interfaces\{ Argument, Data };

/**
 * An IMMUTABLE object that can hold things. Useful as a DTO.
 */
final class Immutable extends ArrayObject implements Data
{

    use GetterTrait;

    /**
     * Sets the object store with values.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        parent::__construct($values, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * {@inheritdoc}
     */
    final public function __clone()
    {
        throw new ReadOnlyException(Data::E_CLONING_DISALLOWED, [':class' => get_class($this)]);
    }

    /**
     * {@inheritdoc}
     */
    final public function append($value)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
    }

    /**
     * {@inheritdoc}
     */
    final public function offsetSet($index, $value)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
    }

    /**
     * {@inheritdoc}
     */
    final public function offsetUnset($index)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
    }

    /**
     * {@inheritdoc}
     */
    final public function exchangeArray($input)
    {
        throw new ReadOnlyException(Data::E_READONLY_INSTANCE, [':class' => get_class($this)]);
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
}
