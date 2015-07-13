<?php

namespace Kohkimakimoto\Luster\Commands;

use Illuminate\Console\Command;

class HelloCommand extends Command
{
    protected $name = 'hello';

    protected $description = 'This is an example command to say helloworld!';

    public function fire()
    {
        $app = $this->laravel['app'];
        $this->output->writeln('Hello world! - '.$app->getName().':'.$app->getVersion());
    }
}
