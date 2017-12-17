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
use Koded\Stdlib\Interfaces\{ ArrayDataFilter, Data, TransformsToArguments };

/**
 * An IMMUTABLE multi purpose class that encapsulates a read-only data.
 * It is useful for passing it around as a DTO.
 */
class Immutable implements Data, ArrayDataFilter, TransformsToArguments, IteratorAggregate, Countable
{

    use AccessorTrait, ArrayDataFilterTrait;

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

    public function toArgument(): Arguments
    {
        return new Arguments($this->storage);
    }
}
