<?php

namespace Kohkimakimoto\Luster\Commands;

use Illuminate\Console\Command;

class InitCommand extends Command
{
    protected $name = 'init';

    protected $description = 'Create Initial luster project.';

    public function fire()
    {
        $filesystem = $this->laravel['files'];

        $rootDir = getcwd();

        // binDir
        $binDir = $rootDir.'/bin';
        if (!$filesystem->exists($binDir)) {
            $filesystem->makeDirectory($binDir, 0755, true);
            $this->output->writeln('<info>Created <comment>'.$binDir.'</comment></info>');
        }

        $commandName = "cmd";
        $binCommandFile = $binDir.'/'.$commandName;
        if (!$filesystem->exists($binCommandFile)) {
            $contents = <<<EOF
#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kohkimakimoto\Luster\Foundation\Application;

\$app = new Application("$commandName", "0.1.0");
\$app->setBasePath(realpath(__DIR__."/.."));
\$app->register([
    'Illuminate\Database\DatabaseServiceProvider',
    'Illuminate\Database\MigrationServiceProvider',
    'Illuminate\Database\SeedServiceProvider',
]);
\$app->command([
    'Kohkimakimoto\Luster\Commands\HelloCommand',
    'Kohkimakimoto\Luster\Commands\ConfigCommand',
]);

\$app->run();

EOF;
            $filesystem->put($binCommandFile, $contents);
            chmod($binCommandFile, 0755);
            $this->output->writeln('<info>Created <comment>'.$binCommandFile.'</comment></info>');
        }

        // config
        $configDir = $rootDir.'/config';
        if (!$filesystem->exists($configDir)) {
            $filesystem->makeDirectory($configDir, 0755, true);
            $this->output->writeln('<info>Created <comment>'.$configDir.'</comment></info>');
        }

        // config/database.php
        $configDatabaseFile = $configDir.'/database.php';
        if (!$filesystem->exists($configDatabaseFile)) {
            $contents = <<<EOF
<?php
return [
    'fetch' => PDO::FETCH_CLASS,
    'default' => 'sqlite',
    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => __DIR__.'/../storage/database.sqlite',
            'prefix'   => '',
        ],
    ],
    'migrations' => 'migrations',
];

EOF;
            $filesystem->put($configDatabaseFile, $contents);
            $this->output->writeln('<info>Created <comment>'.$configDatabaseFile.'</comment></info>');
        }

        // database/migrations
        $databaseMigrationsDir = $rootDir.'/database/migrations';
        if (!$filesystem->exists($databaseMigrationsDir)) {
            $filesystem->makeDirectory($databaseMigrationsDir, 0755, true);
            $this->output->writeln('<info>Created <comment>'.$databaseMigrationsDir.'</comment></info>');
        }

        // storage
        $storageDir = $rootDir.'/storage';
        if (!$filesystem->exists($storageDir)) {
            $filesystem->makeDirectory($storageDir, 0755, true);
            $this->output->writeln('<info>Created <comment>'.$storageDir.'</comment></info>');
        }

        // storage/database.sqlite
        $storageDatabaseFile = $storageDir.'/database.sqlite';
        if (!$filesystem->exists($storageDatabaseFile)) {
            $filesystem->put($storageDatabaseFile, null);
            $this->output->writeln('<info>Created <comment>'.$storageDatabaseFile.'</comment></info>');
        }

        // srcDir
        $srcDir = $rootDir.'/src';
        if (!$filesystem->exists($srcDir)) {
            $filesystem->makeDirectory($srcDir, 0755, true);
            $this->output->writeln('<info>Created <comment>'.$srcDir.'</comment></info>');
        }
    }
}
