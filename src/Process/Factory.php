<?php

namespace Kohkimakimoto\Luster\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

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
        $output = $this->app['console.output'];

        $errOutput = null;
        if ($output instanceof ConsoleOutputInterface) {
            $errOutput = $output->getErrorOutput();
        }

        return $this->make($cmd)->run(function ($type, $buffer) use ($output, $errOutput) {
            if (Process::ERR === $type && $errOutput) {
                $errOutput->write($buffer);
            } else {
                $output->write($buffer);
            }
        });
    }
}
