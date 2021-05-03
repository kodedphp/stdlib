<?php

namespace Tests\Koded\Stdlib;

use Exception;
use Koded\Stdlib\Config;
use PHPUnit\Framework\TestCase;
use function Koded\Stdlib\env;

class ConfigTest extends TestCase
{
    public function test_should_load_defaults_from_other_instance()
    {
        $config = new Config('', new MockOtherConfigInstance);
        $this->assertSame([1, 2, 3], $config->list);
    }

    public function test_that_build_method_should_throw_exception()
    {
        $this->expectException(Exception::class);
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

        $this->assertSame('koded/stdlib', $config->name);
    }

    public function test_should_load_json_file_from_absolute_path()
    {
        $config = new Config;
        $config->fromJsonFile(__DIR__ . '/../composer.json');

        $this->assertSame('koded/stdlib', $config->name);
    }

    public function test_should_load_options_from_object_instance()
    {
        $config = new Config;
        $this->assertNull($config->foo);

        $config->fromObject(new MockOtherConfigInstance);
        $this->assertSame('bar', $config->foo);
    }

    public function test_should_load_options_from_object_fqn()
    {
        $config = new Config;
        $this->assertNull($config->foo);

        $config->fromObject(MockOtherConfigInstance::class);
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
        $config->fromEnvFile(__DIR__ . '/fixtures/.env');


        // Scalar type values are preserved
        $this->assertSame(include __DIR__ . '/fixtures/expected-env-data.php', env());
        $this->assertSame(42, env('KEY_1'));
        $this->assertSame('value3', env('KEY_3'));
        $this->assertSame(true, env('KEY_4'));
        $this->assertSame(null, env('KEY_5'));

        // ENV variable values are mutated to strings
        $this->assertSame('42', \getenv('KEY_1'));
        $this->assertSame('value3', \getenv('KEY_3'));
        $this->assertSame('1', \getenv('KEY_4'));
        $this->assertSame('null', \getenv('KEY_5'));
    }

    public function test_should_return_empty_if_env_file_was_not_found()
    {
        $config = new Config;
        $config->fromEnvFile(__DIR__ . '/fixtures/non-existing.env');
        $this->assertSame([], env());
    }

    public function test_should_load_env_file_and_trim_the_namespace()
    {
        $config = new Config(getcwd());
        $config->fromEnvFile('Tests/fixtures/.env', 'KEY_');
        $this->assertSame(include __DIR__ . '/fixtures/expected-env-trim-ns.php', env());
    }

    public function test_should_load_from_env_variable()
    {
        \putenv('CONFIG_FILE=Tests/fixtures/nested-array.php');
        $config = new Config;
        $config->fromEnvVariable('CONFIG_FILE');

        $this->assertSame('found me', $config->find('array.key3.key3-1.key3-1-1'));
    }

    public function test_should_not_populate_global_env_array()
    {
        $_ENV = []; // clear
        $config = new Config;
        $config->fromEnvFile(__DIR__ . '/fixtures/.env');

        $this->assertArrayNotHasKey('KEY_1', $_ENV);
        $this->assertArrayNotHasKey('KEY_3', $_ENV);
        $this->assertArrayNotHasKey('KEY_4', $_ENV);
        $this->assertArrayNotHasKey('KEY_5', $_ENV);

        $this->assertSame('42', \getenv('KEY_1', true));
        $this->assertSame('value3', \getenv('KEY_3', true));
        $this->assertSame('1', \getenv('KEY_4', true));
        $this->assertSame('null', \getenv('KEY_5', true));
    }

    public function test_should_load_ini_file_from_env_variable()
    {
        \putenv('CONFIG_FILE=Tests/fixtures/config-test.ini');
        $config = new Config;
        $config->fromEnvVariable('CONFIG_FILE');

        $this->assertSame(42, $config->find('section1.key1'));
    }

    public function test_should_throw_exception_on_bad_method_call()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to load the configuration file');

        (new Config)->nonExistentMethod();
    }

    public function test_should_be_silent_on_bad_method_call()
    {
        $config = (new Config)
            ->silent(true)
            ->nonExistentMethod();

        $this->assertInstanceOf(Config::class, $config);
    }

    public function test_should_fail_loading_from_env_non_existing_file()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to load the configuration file');

        \putenv('CONFIG_FILE=non-existent.conf');

        (new Config)->fromEnvVariable('CONFIG_FILE');
    }

    public function test_should_throw_exception_from_empty_env_variable_value()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The environment variable "CONFIG_FILE" is not set');

        \putenv('CONFIG_FILE=');

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
        $config = (new Config)->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5', 'UNKNOWN_VAR']);

        $expected = include __DIR__ . '/fixtures/expected-data-lowercase.php';
        $expected['unknown_var'] = null;
        $this->assertSame($expected, $config->toArray());
    }

    public function test_should_trim_names_from_environment_variables()
    {
        \putenv('KEY_4=true');
        $config = (new Config)->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5'], 'KEY_');

        $this->assertSame(include __DIR__ . '/fixtures/expected-env-trim-ns.php', $config->toArray());
    }

    public function test_should_not_alter_the_keys_from_environment_variables()
    {
        \putenv('KEY_4=true');
        $config = new Config;
        $config->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5'], '', false);

        $this->assertSame(include __DIR__ . '/fixtures/expected-env-data.php', $config->toArray());
    }

    public function test_should_create_arguments_object_using_a_namespace()
    {
        \putenv('KEY_4=true');
        $config = new Config;
        $config->fromEnvironment(['KEY_1', 'KEY_3', 'KEY_4', 'KEY_5']);

        $arguments = $config->namespace('KEY_');

        $this->assertInstanceOf(Config::class, $arguments);
        $this->assertSame(include __DIR__ . '/fixtures/expected-data-lowercase.php', $arguments->toArray());
    }

    public function test_ini_sections_parsing()
    {
        $config = new Config;
        $config->fromIniFile(__DIR__ . '/fixtures/config-sections.ini');
        $this->assertSame(include_once __DIR__ . '/fixtures/config-sections.php', $config->toArray());
    }
}

class MockOtherConfigInstance extends Config
{

    public function __construct()
    {
        parent::__construct();
        $this->fromPhpFile(__DIR__ . '/fixtures/nested-array.php');
    }
}
