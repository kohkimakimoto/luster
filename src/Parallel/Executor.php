<?php

namespace Kohkimakimoto\Luster\Parallel;

class Executor
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function each($arr, $callback = null)
    {
        $manager = new ParallelManager($this->app['console.input'], $this->app['console.output']);
        return $manager->each($arr, $callback);
    }
}
