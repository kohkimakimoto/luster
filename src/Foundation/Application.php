<?php

namespace Kohkimakimoto\Luster\Foundation;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application as LaravelApplicationContract;
use Illuminate\Foundation\Composer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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

    protected $environment;

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = array();

    /**
     * constructor.
     */
    public function __construct($params = [])
    {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
        mb_internal_encoding('UTF-8');
        error_reporting(-1);

        $cwd = getcwd();
        if (!$cwd) {
            $cwd = ".";
        }

        $params = array_merge(['name' => 'luster', 'version' => 'dev', 'basePath' => $cwd], $params);

        $this->name = $params['name'];
        $this->version = $params['version'];
        $this->basePath = $params['basePath'];

        $this->registerBaseBindingsAndServiceProviders();

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
     * @param  string  $path
     * @return $this
     */
    public function useConfigPath($path)
    {
        $this->configPath = $path;

        $this->instance('path.config', $path);

        return $this;
    }

    public function setEnvironment($env)
    {
        $this->environment = $env;
    }

    public function environment()
    {
        return $this->environment ?: 'production';
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
        $this->singleton('config', function () {
            return new Repository();
        });
        $this->singleton('composer', function ($app) {
            return new Composer($app->make('files'), $this->basePath());
        });
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
        $this->loadedProviders[$providerName] = true;
        $provider->register();
        $provider->boot();
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
	 * @param  \Illuminate\Contracts\Foundation\Application  $app
	 * @return array
	 */
	protected function getConfigurationFiles()
	{
		$files = [];

		foreach (Finder::create()->files()->name('*.php')->in($this->configPath()) as $file)
		{
			$nesting = $this->getConfigurationNesting($file);
			$files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
		}

		return $files;
	}

	/**
	 * Get the configuration file nesting path.
	 *
	 * @param  \Symfony\Component\Finder\SplFileInfo  $file
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

    public function run()
    {
        $this->loadConfiguration();

        return $this['cli']->run();
    }
}
