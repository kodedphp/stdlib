<?php

namespace Koded\Stdlib;

use Koded\Stdlib\Interfaces\Data;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function test_should_load_defaults()
    {
        $config = new Config('', new OtherConfigInstance);
        $this->assertSame([1, 2, 3], $config->list);
    }

    public function test_that_build_method_should_throw_exception()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Configuration factory should implement the method ');
        (new Config)->build('');
    }

    public function test_should_load_parameters()
    {
        $config = new Config;
        $this->assertNull($config->key1);

        $config->withParameters(['key1' => 'value1']);
        $this->assertSame('value1', $config->key1);
    }

    public function test_should_load_json_file_from_relative_path()
    {
        $config = new Config;
        $config->fromJsonFile('composer.json');

        $this->assertSame('kodedphp/stdlib', $config->name);
    }

    public function test_should_load_json_file_from_absolute_path()
    {
        $config = new Config;
        $config->fromJsonFile(__DIR__ . '/../composer.json');

        $this->assertSame('kodedphp/stdlib', $config->name);
    }

    public function test_should_load_options_from_object_instance()
    {
        $config = new Config;
        $this->assertNull($config->foo);

        $config->fromObject(new OtherConfigInstance);
        $this->assertSame('bar', $config->foo);
    }

    public function test_should_load_options_from_object_fqn()
    {
        $config = new Config;
        $this->assertNull($config->foo);

        $config->fromObject(OtherConfigInstance::class);
        $this->assertSame('bar', $config->foo);
    }

    public function test_should_load_ini_file()
    {
        $config = new Config;
        $config->fromIniFile('Tests/fixtures/config-test.ini');
        $this->assertSame(include __DIR__ . '/fixtures/expected-ini-data.php', $config->section1);
    }

    /*
     * .env can alter keys with namespace
     *
     */
    public function test_should_load_env_file_as_is()
    {
        $config = new Config;
        $config->fromEnvFile('Tests/fixtures/.env');
        $this->assertSame(include __DIR__ . '/fixtures/expected-env-data.php', $config->toArray());
    }

    public function test_should_load_env_file_and_trim_the_namespace()
    {
        $config = new Config(getcwd());
        $config->fromEnvFile('Tests/fixtures/.env', 'KEY_');
        $this->assertSame(include __DIR__ . '/fixtures/expected-env-trim-ns.php', $config->toArray());
    }

    public function test_should_load_from_env_variable()
    {
        putenv('CONFIG_FILE=Tests/fixtures/nested_array.php');
        $config = new Config;
        $config->fromEnvVariable('CONFIG_FILE');

        $this->assertSame('found me', $config->find('array.key3.key3-1.key3-1-1'));
    }

    public function test_should_fail_loading_from_env_non_existing_file()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to load the configuration file');

        putenv('CONFIG_FILE=non-existent.conf');

        (new Config)->fromEnvVariable('CONFIG_FILE');
    }

    public function test_should_throw_exception_from_empty_env_variable_value()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The environment variable "CONFIG_FILE" is not set');

        putenv('CONFIG_FILE=');

        (new Config)->fromEnvVariable('CONFIG_FILE');
    }

    public function test_should_silently_fail_loading_from_invalid_env_variable_value()
    {
        $config = (new Config)->silent(true)->fromEnvVariable('INVALID_CONFIG_FILE');
        $this->assertInstanceOf(Config::class, $config);
    }

    public function test_should_lowercase_names_from_environment_variables()
    {
        // ye..
        putenv('KEY_4=true');
        $config = new Config;
        $config->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5', 'UNKNOWN_VAR']);

        $expected = include __DIR__ . '/fixtures/expected-data-lowercase.php';
        $expected['unknown_var'] = '';
        $this->assertSame($expected, $config->toArray());
    }

    public function test_should_trim_names_from_environment_variables()
    {
        // don't ask
        putenv('KEY_4=true');
        $config = new Config;
        $config->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5'], 'KEY_');

        $this->assertSame(include __DIR__ . '/fixtures/expected-env-trim-ns.php', $config->toArray());
    }

    public function test_should_not_alter_the_keys_from_environment_variables()
    {
        // don't ask
        putenv('KEY_4=true');
        $config = new Config;
        $config->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5'], '', false);

        $this->assertSame(include __DIR__ . '/fixtures/expected-env-data.php', $config->toArray());
    }

    public function test_should_create_arguments_object_using_a_namespace()
    {
        // don't ask
        putenv('KEY_4=true');
        $config = new Config;
        $config->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5']);

        $arguments = $config->getNamespace('KEY_');

        $this->assertInstanceOf(Data::class, $arguments);
        $this->assertSame(include __DIR__ . '/fixtures/expected-data-lowercase.php', $arguments->toArray());
    }


}

class OtherConfigInstance extends Config
{

    public function __construct()
    {
        parent::__construct('');
        $this->fromPhpFile(__DIR__ . '/fixtures/nested_array.php');
    }
}