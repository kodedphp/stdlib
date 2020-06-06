<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 */

namespace Koded\Stdlib;

use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * An IMMUTABLE, multi purpose class that encapsulates a read-only data.
 * It is useful for passing it around as a DTO.
 */
class Immutable implements Data,
                           ArrayDataFilter,
                           TransformsToArguments,
                           IteratorAggregate,
                           Countable,
                           JsonSerializable
{

    use AccessorTrait, ArrayDataFilterTrait;

    protected $storage = [];

    public function __construct(array $values)
    {
        $this->storage = $values;
    }

    public function toArguments(): Arguments
    {
        return new Arguments($this->storage);
    }
}
