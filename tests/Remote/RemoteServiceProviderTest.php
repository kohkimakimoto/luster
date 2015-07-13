<?php

namespace Test\Kohkimakimoto\Luster\Remote;

use Kohkimakimoto\Luster\Foundation\Application;
use Kohkimakimoto\Luster\Remote\Facades\Remote;
use Kohkimakimoto\Luster\Remote\Server;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class RemoteServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register([
            'Kohkimakimoto\Luster\Remote\RemoteServiceProvider',
        ]);
        $this->app->instance('console.input', new ArrayInput([]));
        $this->app->instance('console.output', new BufferedOutput());
    }

    public function testRegister()
    {
        $communicator = $this->app['remote.communicator'];
        $this->assertEquals(true, $communicator instanceof \Kohkimakimoto\Luster\Remote\Communicator);
    }

    public function testRunWithFacade()
    {
    }
}
