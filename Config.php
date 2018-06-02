<?php declare(strict_types=1);

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

use Exception;
use Koded\Stdlib\Interfaces\{ Configuration, ConfigurationFactory, Data };

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
class Config extends Arguments implements ConfigurationFactory
{

    /** @var string */
    public $rootPath = '';

    /** @var bool */
    private $silent = false;

    /**
     * Config constructor.
     *
     * @param string          $rootPath Path to which files are read relative from.
     *                                  When the config object is created by an application/library
     *                                  this is the application's root path
     * @param Interfaces\Data $defaults [optional] An Optional config object with default values
     */
    public function __construct(string $rootPath = '', Data $defaults = null)
    {
        parent::__construct($defaults ? $defaults->toArray() : []);
        $this->rootPath = $rootPath ?: getcwd();
    }

    /**
     * Bad method calls can be suppressed and allow the app
     * to continue execution by setting the silent(true).
     *
     * The calling app should handle their configuration appropriately.
     *
     * @param string $name Method name
     * @param array $arguments [optional]
     *
     * @return ConfigurationFactory
     * @throws Exception
     */
    public function __call($name, $arguments): ConfigurationFactory
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

    public function withParameters(array $parameters): ConfigurationFactory
    {
        return $this->import($parameters);
    }

    public function fromObject($object): ConfigurationFactory
    {
        if (is_string($object) && class_exists($object)) {
            $object = new $object;
        }

        $this->rootPath = $object->rootPath ?: $this->rootPath;

        return $this->import(iterator_to_array($object));
    }

    public function fromJsonFile(string $filename): ConfigurationFactory
    {
        return $this->loadDataFrom($filename, function($filename) {
            /** @noinspection PhpIncludeInspection */
            return json_decode(file_get_contents($filename), true);
        });
    }

    public function fromIniFile(string $filename): ConfigurationFactory
    {
        return $this->loadDataFrom($filename, function($filename) {
            return parse_ini_file($filename, true, INI_SCANNER_TYPED) ?: [];
        });
    }

    public function fromEnvFile(string $filename, string $namespace = ''): ConfigurationFactory
    {
        return $this->loadDataFrom($filename, function($filename) use ($namespace) {
            try {
                $data = parse_ini_file($filename, false, INI_SCANNER_TYPED);
            } catch (Exception $e) {
                return [];
            }

            foreach ($data as $key => $value) {
                $_ENV[$key] = $value;

                if (null !== $value) {
                    putenv($key . '=' . $value);
                }
            }

            if (empty($namespace)) {
                return $data;
            }

            return $this->filter($data, $namespace);
        });
    }

    public function fromEnvVariable(string $variable): ConfigurationFactory
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

        return $this;
    }

    public function fromPhpFile(string $filename): ConfigurationFactory
    {
        return $this->loadDataFrom($filename, function($filename) {
            /** @noinspection PhpIncludeInspection */;
            return include $filename;
        });
    }

    public function fromEnvironment(
        array $variableNames,
        string $namespace = '',
        bool $lowercase = true,
        bool $trim = true
    ): ConfigurationFactory
    {
        $data = [];
        foreach ($variableNames as $variable) {
            $value = getenv($variable);
            $data[] = $variable . '=' . (false === $value ? 'null' : $value);
        }

        $data = parse_ini_string(join(PHP_EOL, $data), false, INI_SCANNER_TYPED) ?: [];
        $this->import($this->filter($data, $namespace, $lowercase, $trim));

        return $this;
    }

    public function silent(bool $silent): ConfigurationFactory
    {
        $this->silent = $silent;

        return $this;
    }

    public function namespace(string $prefix, bool $lowercase = true, bool $trim = true)
    {
        return (new static($this->rootPath))->import($this->filter($this->toArray(), $prefix, $lowercase, $trim));
    }

    protected function loadDataFrom(string $filename, callable $loader): ConfigurationFactory
    {
        $file = ('/' === $filename[0]) ? $filename : $this->rootPath . '/' . ltrim($filename, '/');
        $this->import($loader($file));

        return $this;
    }
}
