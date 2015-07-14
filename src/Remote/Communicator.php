<?php

namespace Kohkimakimoto\Luster\Remote;

class Communicator
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function run($server, $options, $command = null)
    {
        if (is_array($server)) {
            $server = new Server($server);
        }

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
