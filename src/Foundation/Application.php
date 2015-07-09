<?php

namespace Kohkimakimoto\Luster\Foundation;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application as LaravelApplicationContract;
use Illuminate\Foundation\Composer;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Dotenv;
use InvalidArgumentException;

class Application extends Container implements LaravelApplicationContract
{
    /**
     * The base path for the Laravel installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * Vertion of the application.
     *
     * @var string
     */
    protected $version;

    /**
     * The custom database path defined by the developer.
     *
     * @var string
     */
    protected $databasePath;

    /**
     * The custom storage path defined by the developer.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * The custom config path defined by the developer.
     *
     * @var string
     */
    protected $configPath;

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = array();

	/**
	 * The environment file to load during bootstrapping.
	 *
	 * @var string
	 */
	protected $environmentFile = '.env';

    /**
     * constructor.
     */
    public function __construct($name = 'luster', $version = 'dev')
    {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
        mb_internal_encoding('UTF-8');
        error_reporting(-1);

        $cwd = getcwd();
        if (!$cwd) {
            $cwd = '.';
        }

        $this->name = $name;
        $this->version = $version;
        $this->basePath = $cwd;

        $this->registerBaseBindingsAndServiceProviders();
        $this->registerBaseFacades();
        $this->registerCli($this->name, $this->version);
    }

    /**
     * Get the version.
     *
     * @return string
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Set the base path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function useBasePath($path)
    {
        $this->basePath = $path;

        return $this;
    }

    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath()
    {
        return $this->databasePath ?: $this->basePath.DIRECTORY_SEPARATOR.'database';
    }

    /**
     * Set the database directory.
     *
     * @param string $path
     *
     * @return $this
     */
    public function useDatabasePath($path)
    {
        $this->databasePath = $path;

        $this->instance('path.database', $path);

        return $this;
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?: $this->basePath.DIRECTORY_SEPARATOR.'storage';
    }

    /**
     * Set the storage directory.
     *
     * @param string $path
     *
     * @return $this
     */
    public function useStoragePath($path)
    {
        $this->configPath = $path;

        $this->instance('path.storage', $path);

        return $this;
    }

    /**
     * Get the path to the application configuration files.
     *
     * @return string
     */
    public function configPath()
    {
        return $this->configPath ?: $this->basePath.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * Set a custom configuration path for the application.
     *
     * @param string $path
     *
     * @return $this
     */
    public function useConfigPath($path)
    {
        $this->configPath = $path;

        $this->instance('path.config', $path);

        return $this;
    }

    public function environment()
    {
        return $this['env'];
    }

	/**
	 * Set the environment file to be loaded during bootstrapping.
	 *
	 * @param  string  $file
	 * @return $this
	 */
	public function loadEnvironmentFrom($file)
	{
		$this->environmentFile = $file;

		return $this;
	}

	/**
	 * Get the environment file the application is using.
	 *
	 * @return string
	 */
	public function environmentFile()
	{
		return $this->environmentFile ?: '.env';
	}

    public function isDownForMaintenance()
    {
        return false;
    }

    public function registerConfiguredProviders()
    {
        // unsupported method.
    }

    /**
     * Register the basic bindings into the container.
     */
    protected function registerBaseBindingsAndServiceProviders()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->register('Illuminate\Events\EventServiceProvider');
        $this->register('Illuminate\Filesystem\FilesystemServiceProvider');
        $this->register('Illuminate\View\ViewServiceProvider');

        $this->singleton('config', function () {
            return new Repository();
        });
        $this->singleton('composer', function ($app) {
            return new Composer($app->make('files'), $this->basePath());
        });
    }

    protected function registerBaseFacades()
    {
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this);

        class_alias('Illuminate\Support\Facades\App', 'App');
        class_alias('Illuminate\Support\Facades\Schema', 'Schema');
        class_alias('Illuminate\Support\Facades\Event', 'Event');
        class_alias('Illuminate\Support\Facades\DB', 'DB');
        class_alias('Illuminate\Database\Eloquent\Model', 'Eloquent');
        class_alias('Illuminate\Support\Facades\File', 'File');
        class_alias('Illuminate\Support\Facades\Config', 'Config');
        class_alias('Illuminate\Support\Facades\Blade', 'Blade');
        class_alias('Illuminate\Support\Facades\View', 'View');
    }

    protected function registerCli($name, $version)
    {
        $this->instance('cli', new Cli($name, $version, $this, $this['events']));
    }

    /**
     * Register a service provider with the application.
     *
     * @param \Illuminate\Support\ServiceProvider|string $provider
     * @param array                                      $options
     * @param bool                                       $force
     */
    public function register($provider, $options = array(), $force = false)
    {
        if (is_array($provider)) {
            foreach ($provider as $i => $p) {
                $this->register($p);
            }

            return;
        }

        if (!$provider instanceof ServiceProvider) {
            $provider = new $provider($this);
        }
        if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
            return;
        }
        $this->loadedProviders[$providerName] = $provider;
        $provider->register();
    }

    public function registerDeferredProvider($provider, $service = null)
    {
        return $this->register($provider);
    }

    public function boot()
    {
        // unsupported method.
    }

    /**
     * Register a new boot listener.
     *
     * @param mixed $callback
     */
    public function booting($callback)
    {
        // unsupported method.
    }
    /**
     * Register a new "booted" listener.
     *
     * @param mixed $callback
     */
    public function booted($callback)
    {
        // unsupported method.
    }

    protected function loadConfiguration()
    {
        if (!is_dir($this->configPath())) {
            return;
        }

        //
        // It refers to https://raw.githubusercontent.com/laravel/framework/5.0/src/Illuminate/Foundation/Bootstrap/LoadConfiguration.php
        //

        foreach ($this->getConfigurationFiles() as $key => $path) {
            $this['config']->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return array
     */
    protected function getConfigurationFiles()
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($this->configPath()) as $file) {
            $nesting = $this->getConfigurationNesting($file);
            $files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $file
     *
     * @return string
     */
    private function getConfigurationNesting(SplFileInfo $file)
    {
        $directory = dirname($file->getRealPath());
        $configPath = realpath($this->configPath());

        if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
        }

        return $tree;
    }

    public function add($command)
    {
        if (is_array($command)) {
            foreach ($command as $i => $c) {
                if (is_string($c)) {
                    $c = new $c();
                }
                $this['cli']->add($c);
            }
        } else {
            if (is_string($command)) {
                $command = new $command();
            }
            $this['cli']->add($command);
        }
    }

    public function command($command)
    {
        $this->add($command);
    }

    protected function detectEnvironment()
    {
		try {
			Dotenv::load($this->basePath(), $this->environmentFile());
		} catch (InvalidArgumentException $e) {

        }

        $env = env('APP_ENV', 'production');

        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;
        $value = array_first($args, function($k, $v) {
			return starts_with($v, '--env');
		});

		if (!is_null($value)) {
			$env = head(array_slice(explode('=', $value), 1));
		}

        $this['env'] = $env;
    }

    public function run()
    {
        $this->detectEnvironment();
        $this->loadConfiguration();

        foreach ($this->loadedProviders as $name => $provider) {
            $provider->boot();
        }

        return $this['cli']->run();
    }
}
