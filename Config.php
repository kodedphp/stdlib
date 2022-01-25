<?php declare(strict_types=1);

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 */

namespace Koded\Stdlib;

use Exception;
use function call_user_func;
use function class_exists;
use function current;
use function error_get_last;
use function error_log;
use function file_get_contents;
use function getcwd;
use function getenv;
use function is_string;
use function iterator_to_array;
use function join;
use function json_decode;
use function parse_ini_file;
use function parse_ini_string;
use function pathinfo;
use function strtr;
use function ucfirst;

/**
 * Class Config works as a parameter bag that provides ways to fill it
 * from files or other Config instances. There are 2 common patterns
 * to populate the config,
 *
 * either you can fill the Config instance from config files:
 *
 *     $app->config()->fromPhpFile('myconfig.php');
 *     $app->config()->fromJsonFile('myconfig.json');
 *     $app->config()->fromEnvFile('.env');
 *     $app->config()->fromIniFile('myconfig.ini');
 *
 * or alternatively you can define the configuration options in the instance
 * that calls `fromObject`,
 *
 *     $app->config()->fromObject(MyConfig::class);
 *     $app->config()->fromObject($myconfig); // $myconfig is instance of Config
 *
 * Other interesting way to load configuration is from an environment variable
 * that points to a file
 *
 *     $app->config()->fromEnvVariable('MY_APP_SETTINGS');
 *
 * In this case, before launching the application you have to set the env variable
 * to the file you want to use. On Linux and OSX use the export statement
 *
 *     export MY_APP_SETTINGS='/path/to/config/file.php'
 *
 * or somewhere in your app bootstrap phase before constructing the Api instance
 *
 *     putenv('MY_APP_SETTINGS=/path/to/config/file.php');
 *
 */
class Config extends Arguments implements Configuration
{
    public string $rootPath = '';
    private bool $silent = false;

    /**
     * Config constructor.
     *
     * @param string $rootPath Path to which files are read relative from.
     *                                  When the config object is created by an application/library
     *                                  this is the application's root path
     * @param Data|null $defaults [optional] An Optional config object with default values
     */
    public function __construct(string $rootPath = '', Data $defaults = null)
    {
        parent::__construct($defaults ? $defaults->toArray() : []);
        if (!$this->rootPath = $rootPath) {
            $this->rootPath = getcwd();
        }
    }

    /**
     * Bad method calls can be suppressed and allow the app
     * to continue execution by setting the silent(true).
     *
     * The app should handle their configuration appropriately.
     *
     * @param string $name Method name
     * @param array|null $arguments [optional]
     * @return Configuration
     * @throws Exception
     */
    public function __call(string $name, array|null $arguments): Configuration
    {
        if (false === $this->silent) {
            throw new Exception('Unable to load the configuration file ' . current($arguments));
        }
        return $this;
    }

    public function build(string $context): Configuration
    {
        throw new Exception('Configuration factory should implement the method ' . __METHOD__);
    }

    public function withParameters(array $parameters): Configuration
    {
        return $this->import($parameters);
    }

    public function fromObject(object|string $object): Configuration
    {
        if (is_string($object) && class_exists($object)) {
            $object = new $object;
        }
        $this->rootPath = $object->rootPath ?: $this->rootPath;
        return $this->import(iterator_to_array($object));
    }

    public function fromJsonFile(string $filename): Configuration
    {
        return $this->loadDataFrom($filename,
            fn() => json_decode(file_get_contents($filename), true)
        );
    }

    public function fromIniFile(string $filename): Configuration
    {
        return $this->loadDataFrom($filename,
            fn() => parse_ini_file($filename, true, INI_SCANNER_TYPED) ?: []
        );
    }

    public function fromEnvFile(string $filename, string $namespace = ''): Configuration
    {
        try {
            $data = parse_ini_file($this->filename($filename), true, INI_SCANNER_TYPED) ?: [];
            env('', null, $this->filter($data, $namespace, false));
        } catch (Exception $e) {
            error_log('[Configuration error]: ' . $e->getMessage());
            env('', null, []);
        } finally {
            return $this;
        }
    }

    public function fromEnvVariable(string $variable): Configuration
    {
        if (false === empty($filename = getenv($variable))) {
            $extension = ucfirst(pathinfo($filename, PATHINFO_EXTENSION));
            return call_user_func([$this, "from{$extension}File"], $filename);
        }
        if (false === $this->silent) {
            throw new Exception(strtr('The environment variable ":variable" is not set
            and as such configuration could not be loaded. Set this variable and
            make it point to a configuration file', [':variable' => $variable]));
        }
        error_log('[Configuration error]: ' . (error_get_last()['message'] ?? "env var: $variable"));
        return $this;
    }

    public function fromPhpFile(string $filename): Configuration
    {
        return $this->loadDataFrom($filename, fn() => include $filename);
    }

    public function fromEnvironment(
        array  $variableNames,
        string $namespace = '',
        bool   $lowercase = true,
        bool   $trim = true): Configuration
    {
        $data = [];
        foreach ($variableNames as $variable) {
            $value = getenv($variable);
            $data[] = $variable . '=' . (false === $value ? 'null' : $value);
        }
        $data = parse_ini_string(join(PHP_EOL, $data), true, INI_SCANNER_TYPED) ?: [];
        $this->import($this->filter($data, $namespace, $lowercase, $trim));
        return $this;
    }

    public function silent(bool $silent): Configuration
    {
        $this->silent = $silent;
        return $this;
    }

    public function namespace(
        string $prefix,
        bool   $lowercase = true,
        bool   $trim = true): static
    {
        return (new static($this->rootPath))
            ->import($this->filter($this->toArray(), $prefix, $lowercase, $trim));
    }

    protected function loadDataFrom(string $filename, callable $loader): Configuration
    {
        return $this->import($loader($this->filename($filename)));
    }

    private function filename(string $filename): string
    {
        return ('/' !== $filename[0])
            ? $this->rootPath . '/' . $filename
            : $filename;
    }
}
