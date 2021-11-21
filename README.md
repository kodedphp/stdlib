Koded Standard Library
======================

A standard library for shareable classes and functions.

[![Latest Stable Version](https://img.shields.io/packagist/v/koded/stdlib.svg)](https://packagist.org/packages/koded/stdlib)
[![Build Status](https://travis-ci.com/kodedphp/stdlib.svg?branch=master)](https://travis-ci.com/kodedphp/stdlib)
[![Code Coverage](https://scrutinizer-ci.com/g/kodedphp/stdlib/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kodedphp/stdlib/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kodedphp/stdlib/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kodedphp/stdlib/?branch=master)
[![Packagist Downloads](https://img.shields.io/packagist/dt/koded/stdlib.svg)](https://packagist.org/packages/koded/stdlib)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)](https://php.net/)


Classes
-------

### `Immutable`
- `get(string $index, $default = null)`
- `has($index): bool`
- `equals(string $propertyA, string $propertyB): bool`
- `find(string $index, $default = null)`
- `extract(array $keys): array`
- `filter(iterable $data,
          string $prefix,
          bool $lowercase = true,
          bool $trim = true): array`
- `namespace(string $prefix, bool $lowercase = true, bool $trim = true)`
- `count()`
- `toArray(): array`
- `toJSON(int $options = 0): string`
- `toXML(string $root): string`
- `toArguments(): Arguments`

### `Arguments`
(implements `Immutable` methods)
- `set(string $index, $value)`
- `import(array $values)`
- `upsert(string $index, $value)`
- `bind(string $index, &$variable)`
- `pull(string $index, $default = null)`
- `delete(string $index)`
- `clear()`
- `toImmutable(): Immutable`


### `ExtendedArguments`
``ExtendedArguments extends Arguments``

- `flatten(): ExtendedArguments`

Supports _dot-notation_ for index names. Example:
```php
$args = new \Koded\Stdlib\ExtendedArguments([
    'foo' => [
        'bar' => [
            'baz' => 42
        ]
    ]
]);

$args->get('foo.bar');
// returns ['baz' => 42]

$args->get('foo.bar.baz');
// returns 42

$args->flatten();
// returns ['foo.bar.baz' => 42]

$args->set('foo.bar', 'baz');
// ['foo' => ['bar' => 'baz']]
```

### `Config`
Class `Config` works as a parameter bag that provides ways to fill it
from files or other `Config` instances. There are 2 common patterns
to populate the config,

either you can fill the Config instance from config files:
```php
$app->config()->fromPhpFile('myconfig.php');
$app->config()->fromJsonFile('myconfig.json');
$app->config()->fromEnvFile('.env');
$app->config()->fromIniFile('myconfig.ini');
```
or alternatively you can define the configuration options in the instance
that calls `fromObject`,
```php
$app->config()->fromObject(MyConfig::class);
$app->config()->fromObject($myconfig); // $myconfig is instance of Config
```
Other interesting way to load configuration is from an environment variable
that points to a file
```php
$app->config()->fromEnvVariable('MY_APP_SETTINGS');
```
In this case, before launching the application you have to set the env variable
to the file you want to use. On Linux and OSX use the export statement
```shell script
export MY_APP_SETTINGS='/path/to/config/file.php'
```
or somewhere in your app bootstrap phase before constructing the Api instance
```php
putenv('MY_APP_SETTINGS=/path/to/config/file.php');
```

- `fromEnvironment(
           array $variableNames,
           string $namespace = '',
           bool $lowercase = true,
           bool $trim = true
       ): Configuration`
- `fromJsonFile(string $file): Configuration`
- `fromPhpFile(string $file): Configuration`
- `fromEnvVariable(string $variable): Configuration`
- `fromIniFile(string $file): Configuration`
- `fromObject($object): Configuration`
- `withParameters(array $parameters): Configuration`
- `silent(bool $silent): Configuration`
- `build(string $context): Configuration`


### `Mime`
- `type(string $extension, int $index = 0): string`
- `types(string $extension): array`
- `supports(string $type): bool`
- `extensions(string $type): array`


### `UUID`
Class UUID generates Universally Unique Identifiers following the [RFC 4122][rfc-4122].

- `v1($address = null): string`
- `v3(string $namespace, $name): string`
- `v4(): string`
- `v5(string $namespace, string $name): string`
- `valid(string $uuid): bool`
- `matches(string $uuid, int $version = 4): bool`

Functions
---------
```php
function arguments(...$values): Argument;
function camel_to_snake_case(string $string): string;
function env(string $name = null, mixed $default = null, array $initialState = null): mixed;
function error_log(string $function, string $message, $data): void;
function htmlencode(string $input, string $encoding = 'UTF-8'): string;
function is_associative(array $array): bool;
function json_serialize($value, int $options = JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES): string;
function json_unserialize(string $json, bool $associative = false);
function now(): DateTimeImmutable;
function randomstring(int $length = 16, string $prefix = '', string $suffix = ''): string;
function rmdir(string $dirname): bool;
function snake_to_camel_case(string $string): string;
function to_delimited_string(string $string, int $delimiter): string;
function to_kebab_string(string $string): string;
function value(...$values): Data;
function xml_serialize(string $root, iterable $data): string;
function xml_unserialize(string $xml): array;
```

Code quality
------------

```shell script
vendor/bin/phpbench run --report=default
vendor/bin/phpunit
```

License
-------
[![Software license](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](LICENSE)

The code is distributed under the terms of [The 3-Clause BSD license](LICENSE).


[rfc-4122]: http://tools.ietf.org/html/rfc4122

