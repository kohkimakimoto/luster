<?php

namespace Kohkimakimoto\Luster\Remote\Facades;

use Illuminate\Support\Facades\Facade;

class Remote extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'remote.communicator';
    }
}
