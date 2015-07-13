<?php
namespace Test\Kohkimakimoto\Luster\Parallel;

use Kohkimakimoto\Luster\Foundation\Application;
use Kohkimakimoto\Luster\Parallel\ParallelServiceProvider;
use Kohkimakimoto\Luster\Parallel\Facades\Parallel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;


class ParallelServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected $app;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->register([
            'Kohkimakimoto\Luster\Parallel\ParallelServiceProvider',
        ]);
        $this->app->instance("console.input", new ArrayInput([]));
        $this->app->instance("console.output", new BufferedOutput());
    }

    public function testRegister()
    {
        $executor = $this->app['parallel.executor'];
        $this->assertEquals(true, $executor instanceof \Kohkimakimoto\Luster\Parallel\Executor);
    }

    public function testEachWithFacade()
    {
        Parallel::each(["web1", "web2"], function($i, $e){
            // checks just running.
        });
    }

}
