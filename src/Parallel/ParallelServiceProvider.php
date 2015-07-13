<?php

namespace Kohkimakimoto\Luster\Parallel;

use Illuminate\Support\ServiceProvider;

class ParallelServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
        $this->app->singleton('parallel.executor', function($app) {
			return new Executor($app);
		});
	}

}
