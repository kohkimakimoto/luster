<?php

namespace Test\Kohkimakimoto\Luster\Commands;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Kohkimakimoto\Luster\Foundation\Application;

class HelloCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * app object
     *
     * @var \Kohkimakimoto\Luster\Foundation\Application
     */
    protected $app;

    protected function setUp()
    {
        $this->app = new Application("test", "0.1.0");
        $this->app->setBasePath(realpath(__DIR__));
        $this->app["cli"]->setAutoExit(false);
        $this->app->command(new \Kohkimakimoto\Luster\Commands\HelloCommand);
    }

    public function testEchoHello()
    {
        $cli = $this->app["cli"];
        $command = $cli->find("hello");
        $tester = new CommandTester($command);
        $tester->execute(["command" => $command->getName()]);
        $this->assertEquals("Hello world! - test:0.1.0\n", $tester->getDisplay());
    }
}
