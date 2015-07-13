<?php

namespace Kohkimakimoto\Luster\Remote;

class Communicator
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function run(Server $server, $options, $command = null)
    {
        $remote = new Remote(
            $server,
            $this->app['console.input'],
            $this->app['console.output']
        );

        if ($command === null) {
            $command = $options;
            $options = [];
        }

        return $remote->run($options, $command);
    }

}
