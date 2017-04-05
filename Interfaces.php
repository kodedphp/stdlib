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

    const E_CLONING_DISALLOWED = 1000;
    const E_READONLY_INSTANCE = 1001;

    /**
     * Value accessor, gets a value by name.
     *
     * @param string $index   The name of the key
     * @param mixed  $default [optional] Default value if property does not exist
     *
     * @return mixed
     */
    public function get(string $index, $default = null);

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
     *  $dataObject->find('foo'); // yields 1
     *  $dataObject->find('bar.baz'); // yields 'gir'
     *
     * @return array
     *
     * @param string $index   The name of the property (dot-notation)
     * @param mixed  $default [optional] Default value if item is not found
     *
     * @return mixed
     */
    public function find(string $index, $default = null);

    /**
     * Extract only the required indexes from the data.
     * The indexes that do not exists will have a NULL value.
     *
     * @param array $keys List of keys to return
     *
     * @return Data A new Data object with filtered values
     */
    public function extract(array $keys): Data;

    /**
     * Checks if the key exist.
     *
     * @param mixed $index The index name
     *
     * @return bool
     */
    public function has($index): bool;

    /**
     * Returns the object state as array.
     *
     * @return array
     */
    public function toArray(): array;
}


interface Argument extends Data
{

    /**
     * Value mutator.
     * Sets a value for a property.
     *
     * @param string $index The name of the property
     * @param mixed  $value The value
     *
     * @return Argument
     */
    public function set(string $index, $value): Argument;

    /**
     * Imports multiple values. The existing are overridden.
     *
     * @param array $values
     *
     * @return Argument
     */
    public function import(array $values): Argument;

    /**
     * "Set once". Add the value(s) for the key if that key does not exists,
     * otherwise it does not set the value.
     *
     * @param string $index The name of the property
     * @param mixed  $value The property value
     *
     * @return Argument
     */
    public function upsert(string $index, $value): Argument;

    /**
     * Sets a variable value by reference.
     *
     * @param string $index    The key name
     * @param mixed  $variable The variable that should be bound
     *
     * @return Argument
     */
    public function bind(string $index, &$variable): Argument;

    /**
     * Gets a value by name and unset it from the storage.
     *
     * @param string $index
     * @param mixed  $default [optional]
     *
     * @return mixed
     */
    public function pull(string $index, $default = null);

    /**
     * Remove a property from the storage array.
     *
     * @param string $index The index name
     *
     * @return Argument
     */
    public function delete(string $index): Argument;

    /**
     * Clears the internal storage.
     *
     * @return Argument
     */
    public function clear(): Argument;
}


interface Configuration extends Data
{
}

interface ConfigurationFactory
{

    /**
     * Imports parameters as they are.
     *
     * @param array $parameters
     *
     * @return ConfigurationFactory
     */
    public function withParameters(array $parameters): ConfigurationFactory;

    /**
     * @param array  $variableNames A list of environment variables to be loaded
     * @param string $namespace     [optional] A prefix used to trim the environment variable names
     * @param bool   $lowercase     [optional ] Convert the names to lowercase
     * @param bool   $trim          [optional] Remove the namespace/prefix from the variable names
     *
     * @return ConfigurationFactory
     */
    public function fromEnvironment(array $variableNames, string $namespace = '', bool $lowercase = true, bool $trim = true): ConfigurationFactory;

    /**
     * Loads the configuration options from JSON file.
     *
     * @param string $file The path to the JSON configuration file
     *
     * @return ConfigurationFactory
     */
    public function fromJsonFile(string $file): ConfigurationFactory;

    /**
     * Loads the configuration options from PHP array stored in a file.
     *
     * @param string $file The path to the PHP configuration file
     *
     * @return ConfigurationFactory
     */
    public function fromPhpFile(string $file): ConfigurationFactory;

    /**
     * Loads the configuration options from `.env` or similar file.
     * The syntax should be the same as the INI file, without sections.
     * Some value types are preserved when possible (null, int and bool)
     *
     * @param string $file The path to the configuration file
     * @param string $namespace [optional]
     *
     * @return ConfigurationFactory
     */
    public function fromEnvFile(string $file, string $namespace = ''): ConfigurationFactory;

    /**
     * Loads the configuration options from environment variable that holds
     * the path to the configuration file.
     *
     * @param string $variable Environment variable with path to the configuration file
     *
     * @return ConfigurationFactory
     */
    public function fromEnvVariable(string $variable): ConfigurationFactory;

    /**
     * Loads the configuration options from INI file.
     * The sections are processed and some value types are preserved when possible (null, int and bool)
     *
     * @param string $file The path to the INI configuration file
     *
     * @return ConfigurationFactory
     */
    public function fromIniFile(string $file): ConfigurationFactory;

    /**
     * Loads the configuration options from other Config instance.
     *
     * @param object|string $object A FQN of the configuration object, or an instance of it
     *
     * @return ConfigurationFactory
     */
    public function fromObject($object): ConfigurationFactory;

    /**
     * Strips the portions of the variable names defined by the namespace value.
     * It can also lowercase the names. Useful to transform the data from ENV variables.
     *
     * @param string $namespace
     * @param bool   $lowercase [optional]
     * @param bool   $trim [optional]
     *
     * @return Data
     */
    public function getNamespace(string $namespace, bool $lowercase = true, bool $trim = true): Data;

    /**
     * Yell if something bad has happened, or pass quietly.
     *
     * @param bool $silent
     *
     * @return ConfigurationFactory
     */
    public function silent(bool $silent): ConfigurationFactory;

    /**
     * Application specific processing of the configuration data.
     *
     * @param string $context The context in question
     *
     * @return Configuration
     */
    public function build(string $context): Configuration;
}