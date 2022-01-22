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


interface ArrayDataFilter
{
    /**
     * Strips the portions of the variable names defined by the namespace value.
     * Returns a filtered array.
     *
     * @param iterable $data      The data should be filtered
     * @param string   $prefix    The namespace prefix for the indexes
     * @param bool     $lowercase [optional] Returned indexes should be in lowercase
     * @param bool     $trim      [optional] To remove the namespace from the index
     *
     * @return array A filtered array
     */
    public function filter(
        iterable $data,
        string $prefix,
        bool $lowercase = true,
        bool $trim = true
    ): array;

    /**
     * Search in the storage array for an array item with a dot-composite name.
     *
     * Example: $dataObject has this data
     *  [
     *      'foo' => 1,
     *      'bar' => [
     *          'baz' => 'gir'
     *      ]
     *  ];
     *
     *  $dataObject->find('foo');     // yields 1
     *  $dataObject->find('bar.baz'); // yields 'gir'
     *
     * @param string $index   The name of the property (dot-notation)
     * @param mixed  $default [optional] Default value if item is not found
     *
     * @return mixed
     */
    public function find(string $index, mixed $default = null): mixed;

    /**
     * Extract only the required indexes from the data.
     * The indexes that do not exists will have a NULL value.
     *
     * @param array $keys List of keys to return
     *
     * @return array An array with filtered values
     */
    public function extract(array $keys): array;
}


interface NamespaceDataFilter
{
    /**
     * Strips the portions of the variable names defined by the prefix value.
     * It can also lowercase the names. Useful to transform the data from ENV variables.
     *
     * Returns a new instance from the original object populated with the filtered data.
     *
     * @param string $prefix    The namespace prefix
     * @param bool   $lowercase [optional] Returned indexes should be lowercase
     * @param bool   $trim      [optional] To remove the namespace from the indexes
     *
     * @return static A new instance of the original object
     */
    public function namespace(string $prefix, bool $lowercase = true, bool $trim = true): static;
}


interface Data
{
    const E_CLONING_DISALLOWED = 1001;
    const E_READONLY_INSTANCE  = 1002;
    const E_PHP_EXCEPTION      = 1003;

    /**
     * Value accessor, gets a value by name.
     *
     * @param string $index   The name of the key
     * @param mixed  $default [optional] Default value if property does not exist
     *
     * @return mixed
     */
    public function get(string $index, mixed $default = null): mixed;

    /**
     * Checks if the key exist.
     *
     * @param mixed $index The index name
     *
     * @return bool
     */
    public function has(mixed $index): bool;

    /**
     * Checks if two properties has equal values.
     *
     * @param string $propertyA Property name
     * @param string $propertyB Property name
     *
     * @return bool
     */
    public function equals(string $propertyA, string $propertyB): bool;

    /**
     * Returns the object state as array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Returns the object as JSON representation.
     *
     * @param int $options JSON options for json_serialize()
     *
     * @return string JSON representation of the object
     * @see http://php.net/manual/en/json.constants.php
     */
    public function toJSON(int $options = 0): string;

    /**
     * Returns the object as XML representation.
     *
     * @param string $root The XML root element name
     *
     * @return string XML representation of the object
     */
    public function toXML(string $root): string;
}


interface Argument extends Data
{
    /**
     * Value mutator.
     * Sets a value for a property.
     *
     * @param int|string $index The name of the property
     * @param mixed  $value The value
     *
     * @return static
     */
    public function set(mixed $index, mixed $value): static;

    /**
     * Imports multiple values. The existing are overridden.
     *
     * @param array $values
     *
     * @return static
     */
    public function import(array $values): static;

    /**
     * "Set once". Add the value(s) for the key if that key does not exists,
     * otherwise it does not set the value.
     *
     * @param string $index The name of the property
     * @param mixed  $value The property value
     *
     * @return static
     */
    public function upsert(string $index, mixed $value): static;

    /**
     * Sets a variable value by reference.
     *
     * @param string $index    The key name
     * @param mixed  $variable The variable that should be bound
     *
     * @return static
     */
    public function bind(string $index, mixed &$variable): static;

    /**
     * Gets a value by name and unset it from the storage.
     *
     * @param string $index
     * @param mixed  $default [optional]
     *
     * @return mixed
     */
    public function pull(string $index, mixed $default = null): mixed;

    /**
     * Remove a property from the storage array.
     *
     * @param string $index The index name
     *
     * @return static
     */
    public function delete(string $index): static;

    /**
     * Clears the internal storage.
     *
     * @return static
     */
    public function clear(): static;
}


interface TransformsToArguments
{
    /**
     * Creates a new instance of Arguments object with current data.
     *
     * @return Arguments
     */
    public function toArguments(): Arguments;
}


interface TransformsToImmutable
{
    /**
     * Creates a new instance of Immutable object with current data.
     *
     * @return Immutable
     */
    public function toImmutable(): Immutable;
}


interface Configuration extends Argument, ArrayDataFilter, NamespaceDataFilter
{
    /**
     * Imports parameters as they are.
     *
     * @param array $parameters
     *
     * @return Configuration
     */
    public function withParameters(array $parameters): Configuration;

    /**
     * @param array  $variableNames A list of environment variables to be loaded
     * @param string $namespace     [optional] A prefix used to trim the environment variable names
     * @param bool   $lowercase     [optional ] Convert the names to lowercase
     * @param bool   $trim          [optional] Remove the namespace/prefix from the variable names
     *
     * @return Configuration
     */
    public function fromEnvironment(
        array $variableNames,
        string $namespace = '',
        bool $lowercase = true,
        bool $trim = true
    ): Configuration;

    /**
     * Loads the configuration options from JSON file.
     *
     * @param string $filename The path to the JSON configuration file
     *
     * @return Configuration
     */
    public function fromJsonFile(string $filename): Configuration;

    /**
     * Loads the configuration options from PHP array stored in a file.
     *
     * @param string $filename The path to the PHP configuration file
     *
     * @return Configuration
     */
    public function fromPhpFile(string $filename): Configuration;

    /**
     * Loads the configuration options from `.env` or similar file.
     * The syntax should be the same as the INI file, without sections.
     * Some value types are preserved when possible (null, int and bool)
     *
     * @param string $filename  The path to the configuration file
     * @param string $namespace [optional]
     *
     * @return Configuration
     */
    public function fromEnvFile(string $filename, string $namespace = ''): Configuration;

    /**
     * Loads the configuration options from environment variable that holds
     * the path to the configuration file.
     *
     * @param string $variable Environment variable with path to the configuration file
     *
     * @return Configuration
     */
    public function fromEnvVariable(string $variable): Configuration;

    /**
     * Loads the configuration options from INI file.
     * The sections are processed and some value types are preserved when possible (null, int and bool)
     *
     * @param string $filename The path to the INI configuration file
     *
     * @return Configuration
     */
    public function fromIniFile(string $filename): Configuration;

    /**
     * Loads the configuration options from other Config instance.
     *
     * @param object|string $object A FQN of the configuration object, or an instance of it
     *
     * @return Configuration
     */
    public function fromObject(object|string $object): Configuration;

    /**
     * Yell if something bad has happened, or pass quietly.
     *
     * @param bool $silent
     *
     * @return Configuration
     */
    public function silent(bool $silent): Configuration;

    /**
     * Application specific processing of the configuration data.
     *
     * @param string $context The context in question
     *
     * @return Configuration
     * @throws \Exception
     */
    public function build(string $context): Configuration;
}


interface Serializer
{
    const
        E_INVALID_SERIALIZER = 409,
        E_MISSING_MODULE     = 424;

    const
        IGBINARY = 'igbinary',
        MSGPACK  = 'msgpack',
        JSON     = 'json',
        XML      = 'xml',
        PHP      = 'php';

    /**
     * Creates a serialized representation of the data.
     *
     * @param mixed $value
     *
     * @return string|null The serialized representation of the data
     */
    public function serialize(mixed $value): ?string;

    /**
     * Recreates the data back from the serialized representation.
     *
     * @param mixed $value The serialized data
     *
     * @return mixed The converted value
     */
    public function unserialize(string $value): mixed;

    /**
     * The string identifier for the serializer object.
     *
     * @return string
     */
    public function type(): string;
}
