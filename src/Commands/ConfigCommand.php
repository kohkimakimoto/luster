<?php

namespace Kohkimakimoto\Luster\Commands;

use Illuminate\Console\Command;

class ConfigCommand extends Command
{
    protected $name = 'config';

    protected $description = 'Show configuration in json format.';

    public function fire()
    {
        $config = $this->laravel['config']->all();
        $this->output->write(json_encode($config));
    }
}
