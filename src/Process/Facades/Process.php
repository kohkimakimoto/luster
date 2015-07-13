<?php

namespace Kohkimakimoto\Luster\Process\Facades;

use Illuminate\Support\Facades\Facade;

class Process extends Facade {

    const ERR = 'err';
    const OUT = 'out';

    const STATUS_READY = 'ready';
    const STATUS_STARTED = 'started';
    const STATUS_TERMINATED = 'terminated';

    const STDIN = 0;
    const STDOUT = 1;
    const STDERR = 2;

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'process.factory';
	}

}
