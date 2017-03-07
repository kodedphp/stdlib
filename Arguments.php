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
use Koded\Stdlib\Interfaces\{ Argument, Data };

/**
 * Arguments is a MUTABLE object that can hold things.
 * It is useful for passing it around as an object that encapsulates data.
 *
 * TIP: Do not create child classes with properties from this one.
 * It will mess up your Zen.
 */
class Arguments extends ArrayObject implements Argument
{

    use GetterTrait, SetterTrait;

    /**
     * DataObject constructor.
     *
     * @param array $values [optional] Initial data
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values, ArrayObject::ARRAY_AS_PROPS);
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