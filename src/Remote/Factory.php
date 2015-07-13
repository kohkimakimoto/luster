<?php

namespace Kohkimakimoto\Luster\Remote;

class Factory
{
    public function make($cmd)
    {
        return new Remote;
    }

    public function run(array $server, $arg2, $arg3 = null)
    {
    }
}
