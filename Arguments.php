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
 * Arguments is a MUTABLE, multipurpose class that encapsulates data.
 * It is useful for passing it around as a DTO.
 *
 * TIP: Avoid creating a child classes with properties from this one.
 * It will mess up your Zen.
 */

#[\AllowDynamicProperties]
class Arguments implements Argument,
                           TransformsToImmutable,
                           NamespaceDataFilter,
                           IteratorAggregate,
                           Countable,
                           JsonSerializable
{
    use AccessorTrait, MutatorTrait, ArrayDataFilterTrait {
        MutatorTrait::__set insteadof AccessorTrait;
    }

    public function __construct(protected array $data = []) {}

    public function __clone() {}

    public function namespace(
        string $prefix,
        bool $lowercase = true,
        bool $trim = true): static
    {
        return new static(
            $this->filter($this->toArray(), $prefix, $lowercase, $trim)
        );
    }

    public function toImmutable(): Immutable
    {
        return new Immutable($this->data);
    }
}
