<?php

namespace Kohkimakimoto\Luster\Process;

use Symfony\Component\Process\Process;

class Factory
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function make($cmd)
    {
        return (new Process($cmd))->setTimeout(null);
    }

    public function run($cmd)
    {
        $outoput = $this->app["console.output"];

        return $this->make($cmd)->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
    }
}
