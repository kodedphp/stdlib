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

use Koded\Stdlib\Interfaces\Data;

/**
 * All private fields are kept intact, but if same property names are
 * set through the c-tor, they are set in the local storage, therefore
 * the values are taken from the storage.
 * This creates a big confusion if you don't know how this works.
 *
 * TIP: Do not create child classes with properties.
 */
trait GetterTrait
{

    /**
     * Returns the value at the specified index.
     * Alias for offsetGet()
     *
     * @param string $offset
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $offset, $default = null)
    {
        return $this->offsetGet($offset, $default);
    }

    /**
     * This overwritten method will return NULL if the $offset is not set in the storage.
     * Once bitten, twice shy.
     *
     * @param string $offset The key name
     * @param null $default [optional] A value if the key does not exist
     *
     * @return mixed
     * @internal
     */
    public function offsetGet($offset, $default = null)
    {
        return parent::offsetGet($offset) ?? $default;
    }

    /**
     * Search in multidimensional arrays with dot-noted key.
     *
     *  Example: $dataObject has this data
     *  [
     *      'foo' => 1,
     *      'bar' => [
     *          'baz' => 'gir'
     *      ]
     *  ];
     *
     *  $dataObject->find('foo'); // yields 1
     *  $dataObject->find('bar.baz'); // yields 'gir'
     *
     * @param string $offset A dot-noted key
     * @param null $default
     *
     * @return array
     */
    public function find(string $offset, $default = null)
    {
        $array = $this->toArray();

        if (isset($array[$offset])) {
            return $array[$offset];
        }

        foreach (explode('.', $offset) as $token) {
            if (!is_array($array) or !array_key_exists($token, $array)) {
                return $default;
            }

            $array = $array[$token];
        }

        return $array;
    }

    /**
     * Returns the whole data as array.
     * Alias for getArrayCopy()
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Extract only the required keys from the data.
     *
     * @param array $keys List of keys to return
     *
     * @return Data returns a new Data object with filtered pairs
     */
    public function filter(array $keys): Data
    {
        $array = [];
        foreach ($keys as $offset) {
            if ($this->offsetExists($offset)) {
                $array[$offset] = $this->$offset;
            }
        }

        return new self($array);
    }
}
