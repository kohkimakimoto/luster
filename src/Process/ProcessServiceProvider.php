<?php

namespace Kohkimakimoto\Luster\Process;

use Illuminate\Support\ServiceProvider;

class ProcessServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
        $this->app->singleton('process.factory', function($app) {
			return new Factory();
		});
	}

}
