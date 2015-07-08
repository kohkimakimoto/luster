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

        // config
        $configDir = $rootDir."/config";
        if (!$filesystem->exists($configDir)) {
            $filesystem->makeDirectory($configDir, 0755, true);
            $this->output->writeln("<info>Created <comment>".$configDir."</comment></info>");
        }

        // config/database.php
        $configDatabaseFile = $configDir."/database.php";
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
            $this->output->writeln("<info>Created <comment>".$configDatabaseFile."</comment></info>");
        }

        // database/migrations
        $databaseMigrationsDir = $rootDir."/database/migrations";
        if (!$filesystem->exists($databaseMigrationsDir)) {
            $filesystem->makeDirectory($databaseMigrationsDir, 0755, true);
            $this->output->writeln("<info>Created <comment>".$databaseMigrationsDir."</comment></info>");
        }

        // storage
        $storageDir = $rootDir."/storage";
        if (!$filesystem->exists($storageDir)) {
            $filesystem->makeDirectory($storageDir, 0755, true);
            $this->output->writeln("<info>Created <comment>".$storageDir."</comment></info>");
        }

        // storage/database.sqlite
        $storageDatabaseFile = $storageDir."/database.sqlite";
        if (!$filesystem->exists($storageDatabaseFile)) {
            $filesystem->put($storageDatabaseFile, null);
            $this->output->writeln("<info>Created <comment>".$storageDatabaseFile."</comment></info>");
        }
    }
}
