<?php

namespace Kohkimakimoto\Luster\Process;

use Symfony\Component\Process\Process;

class Factory
{
    public function make($cmd)
    {
        return (new Process($cmd))->setTimeout(null);
    }

    public function run($cmd, $callback = null)
    {
        return $this->make($cmd)->run($callback);
    }
}
