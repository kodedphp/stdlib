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

namespace Koded\Stdlib\Interfaces;

interface Data
{

    /**
     * Value accessor, gets a value by name.
     *
     * @param string $key     The name of the key
     * @param mixed  $default [optional] Default value if property does not exist
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Search in the storage array for an array item with a dot-composite name.
     *
     * @param string $key     The name of the property (dot-notation)
     * @param mixed  $default [optional] Default value if item is not found
     *
     * @return mixed
     */
    public function find(string $key, $default = null);

    /**
     * Filter out only keys that are in the argument array.
     *
     * @param array $keys The list of keys that are wanted
     *
     * @return Data
     */
    public function filter(array $keys): Data;

    /**
     * Returns the object as array. Supports additional filtering.
     *
     * @throws \Exception
     * @return array
     *
     * WARNING: Pay attention to
     * @link http://us3.php.net/get_object_vars#84260
     * @link http://us3.php.net/manual/en/function.get-object-vars.php#116092
     */
    public function toArray(): array;
}


interface Arguments extends Data
{

    /**
     * Value mutator.
     * Sets a value for a property, or an array of values at once.
     *
     * @param mixed $key   The name of the property
     * @param mixed $value The value
     *
     * @return Arguments
     */
    public function set(string $key, $value): Arguments;

    /**
     * Imports multiple values. The existing are overridden.
     *
     * @param array $values
     *
     * @return Arguments
     */
    public function import(array $values): Arguments;

    /**
     * "Set once". Add the value(s) for the key if that key does not exists,
     * otherwise it does not set the value.
     *
     * @param string $key   The name of the property
     * @param mixed $value The property value
     *
     * @return Arguments
     */
    public function upsert(string $key, $value): Arguments;

    /**
     * Sets a variable value by reference.
     *
     * @param string $key      The key name
     * @param mixed  $variable The variable that should be bound
     *
     * @return Arguments
     */
    public function bind(string $key, &$variable): Arguments;

    /**
     * Gets a value by name and unset it from the storage.
     *
     * @param string $key
     * @param mixed  $default [optional]
     *
     * @return mixed
     */
    public function &pull(string $key, $default = null);

    /**
     * Remove a property from the storage array.
     *
     * @param string $key The property name
     *
     * @return Arguments
     */
    public function delete(string $key): Arguments;
}