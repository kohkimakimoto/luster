<?php

namespace Kohkimakimoto\Luster\Process;

use Illuminate\Support\ServiceProvider;

class ProcessServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('process.factory', function ($app) {
            return new Factory($app);
        });
    }
}
