<?php

namespace Kohkimakimoto\Luster\Remote;

use Illuminate\Support\ServiceProvider;

class RemoteServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('remote.factory', function ($app) {
            return new Factory();
        });
    }
}
