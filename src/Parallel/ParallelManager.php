<?php

namespace Kohkimakimoto\Luster\Parallel;

use ReflectionFunction;

class ParallelManager
{
    protected $entries;

    protected $closure;

    protected $isParallel;

    protected $input;

    protected $output;

    protected $childPids = [];

    public function __construct($input, $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function each($entries = array(), $closure)
    {
        $this->entries = $entries;
        $this->closure = $closure;

        if (!function_exists('pcntl_signal') || !function_exists('pcntl_fork') || !function_exists('pcntl_wait') || !function_exists('posix_kill')) {
            $this->isParallel = false;
            if ($this->output->isDebug()) {
                $this->output->writeln('Running serial mode.');
            }
        } else {
            $this->isParallel = true;

            declare (ticks = 1);
            pcntl_signal(SIGTERM, array($this, 'signalHandler'));
            pcntl_signal(SIGINT, array($this, 'signalHandler'));
        }

        foreach ($this->entries as $key => $entry) {
            if (!$this->isParallel) {
                $this->doRunEntry($this->closure, $key, $entry);
                continue;
            }

            $pid = pcntl_fork();
            if ($pid === -1) {
                // Error
                throw new \RuntimeException('Fork Error.');
            } elseif ($pid) {
                // Parent process
                $this->childPids[$pid] = $entry;
            } else {
                // Child process
                if ($this->output->isDebug()) {
                    $this->output->writeln('Forked process (pid:'.posix_getpid().')');
                }

                $this->doRunEntry($this->closure, $key, $entry);
                exit(0);
            }
        }

        // At the following code, only parent precess runs.
        while (count($this->childPids) > 0) {
            // Keep to wait until to finish all child processes.
            $status = null;
            $pid = pcntl_wait($status);
            if (!$pid) {
                throw new \RuntimeException('pcntl_wait error.');
            }

            if (!array_key_exists($pid, $this->childPids)) {
                throw new \RuntimeException('pcntl_wait error.'.$pid);
            }

            // When a child process is done, removes managed child pid.
            $entry = $this->childPids[$pid];
            unset($this->childPids[$pid]);

            if ($this->output->isDebug()) {
                $this->output->writeln('Finished process (pid:'.$pid.')');
            }
        }
    }

    protected function doRunEntry($closure, $key, $entry)
    {
        $ref = new ReflectionFunction($closure);
        $numberOfParameters = $ref->getNumberOfParameters();

        if ($numberOfParameters == 1) {
            call_user_func($closure, $entry);
        } else {
            call_user_func($closure, $key, $entry);
        }
    }

    public function signalHandler($signo)
    {
        switch ($signo) {
            case SIGTERM:
                $this->output->writeln('<fg=red>Got SIGTERM.</fg=red>');
                $this->killAllChildren();
                exit;

            case SIGINT:
                $this->output->writeln('<fg=red>Got SIGINT.</fg=red>');
                $this->killAllChildren();
                exit;
        }
    }

    public function killAllChildren()
    {
        foreach ($this->childPids as $pid => $host) {
            $this->output->writeln("<fg=red>Sending sigint to child (pid:</fg=red><comment>$pid</comment><fg=red>)</fg=red>");
            $this->killProcess($pid);
        }
    }

    protected function killProcess($pid)
    {
        posix_kill($pid, SIGINT);
    }
}
