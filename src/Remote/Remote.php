<?php

namespace Kohkimakimoto\Luster\Remote;

class Remote
{
    protected $server;

    protected $input;

    protected $output;

    public function __construct(Server $server, $input, $output)
    {
        $this->server = $server;
        $this->input = $input;
        $this->output = $output;
    }

    public function run(array $options, $commandline)
    {
        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        $realCommand = $this->compileRealCommand($commandline, $options);

        $ssh = $this->server->getSSHConnection();
        if (isset($options["timeout"])) {
            $ssh->setTimeout($options["timeout"]);
        } else {
            $ssh->setTimeout(null);
        }

        if ($this->output->isDebug()) {
            $this->output->writeln(
                "<info>Run command: </info>$commandline (actually: <comment>$realCommand</comment>) at "
                .$this->server->host);
        } else {
            $this->output->writeln(
                "<info>Run command: </info>$commandline at "
                .$this->server->host);
        }

        $output = $this->output;
        $resultContent = null;

        $ssh->exec($realCommand, function ($buffer) use ($output, &$resultContent) {
            $output->write($buffer);
            $resultContent .= $buffer;
        });

        $returnCode = $ssh->getExitStatus();

        $result = new Result($returnCode, $resultContent);
        if ($result->isFailed()) {
            $output->writeln($result->getContents());
        }

        return $result;
    }

    protected function compileRealCommand($commandline, $options)
    {
        $realCommand = "";
        $os = php_uname('s');
        if (preg_match('/Windows/i', $os)){
            if (isset($options["user"])) {
                $realCommand .= 'runas /user:' . $options["user"] . ' ';
            }
            $realCommand .= 'cmd.exe /C "';
            if (isset($options["cwd"])) {
                $realCommand .= 'cd ' . $options["cwd"] . ' & ';
            }
            $realCommand .= str_replace('"', '\"', $commandline);
            $realCommand .= '"';
        } else {
            if (isset($options["user"])) {
                $realCommand .= 'sudo -u' . $options["user"] . ' TERM=dumb ';
            }
            $realCommand .= 'bash -l -c "';
            if (isset($options["cwd"])) {
                $realCommand .= 'cd ' . $options["cwd"] . ' && ';
            }
            $realCommand .= str_replace('"', '\"', $commandline);
            $realCommand .= '"';
        }
        return $realCommand;
    }
}
