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
use Koded\Stdlib\Interfaces\{ Arguments, Data };
use LogicException;

/**
 * Class ValueObject is an IMMUTABLE object that can hold things.
 * Useful as a DTO.
 */
final class Immutable extends ArrayObject implements Data
{

    use GetterTrait;

    const E_CLONING_DISALLOWED = 'Cloning the :class is not allowed';
    const E_READONLY_INSTANCE = ':class instance is read-only';

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
        throw new LogicException(strtr(self::E_CLONING_DISALLOWED, [':class' => get_class($this)]));
    }

    /**
     * {@inheritdoc}
     */
    final public function append($value)
    {
        throw new LogicException(strtr(self::E_READONLY_INSTANCE, [':class' => get_class($this)]));
    }

    final public function offsetSet($index, $value)
    {
        throw new LogicException(strtr(self::E_READONLY_INSTANCE, [':class' => get_class($this)]));
    }

    /**
     * {@inheritdoc}
     */
    final public function offsetUnset($index)
    {
        throw new LogicException(strtr(self::E_READONLY_INSTANCE, [':class' => get_class($this)]));
    }

    /**
     * {@inheritdoc}
     */
    final public function exchangeArray($input)
    {
        throw new LogicException(strtr(self::E_READONLY_INSTANCE, [':class' => get_class($this)]));
    }

    /**
     * @experimental
     *
     * @return Arguments
     */
    public function toArgument(): Arguments
    {
        return new Argument($this->toArray());
    }
}
