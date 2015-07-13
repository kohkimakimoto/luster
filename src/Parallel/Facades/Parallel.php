<?php

namespace Kohkimakimoto\Luster\Parallel\Facades;

use Illuminate\Support\Facades\Facade;

class Parallel extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'parallel.executor';
	}

}
