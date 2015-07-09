# Luster

[![Build Status](https://travis-ci.org/kohkimakimoto/luster.svg)](https://travis-ci.org/kohkimakimoto/luster)
[![Latest Stable Version](https://poser.pugx.org/kohkimakimoto/luster/v/stable)](https://packagist.org/packages/kohkimakimoto/luster) [![Total Downloads](https://poser.pugx.org/kohkimakimoto/luster/downloads)](https://packagist.org/packages/kohkimakimoto/luster) [![Latest Unstable Version](https://poser.pugx.org/kohkimakimoto/luster/v/unstable)](https://packagist.org/packages/kohkimakimoto/luster) [![License](https://poser.pugx.org/kohkimakimoto/luster/license)](https://packagist.org/packages/kohkimakimoto/luster)

A command line application framework based on [Laravel](http://laravel.com/).

> Note: Luster 5.0.x is based on Laravel5.0.

## Requirements

* PHP5.4 or later

## Installation

Create `composer.json` for installing via composer..

```
{
    "require": {
        "kohkimakimoto/luster": "5.0.*"
    }
}
```

Run composer install command.

```
composer install
```

## Usage

### Getting Started

Run `luster init` to create your command line app project files.

```
php vendor/bin/luster init
```

You will get some directories and files. Look at `bin/cmd`. It is a executable command file to bootstrap the app. You should rename it.

Run this command.

```
php bin/cmd
```

Did you get messages like the following? It is OK. Luster has been installed correctly.

```
cmd version 0.1.0

Usage:
 command [options] [arguments]

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
...
```

Let's start developing your command line app. Open `bin/cmd` file by your text editor.

```php
#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Kohkimakimoto\Luster\Foundation\Application;

$app = new Application("cmd", "0.1.0");
$app->setBasePath(realpath(__DIR__."/.."));
$app->register([
    // 'Illuminate\Database\DatabaseServiceProvider',
    // 'Illuminate\Database\MigrationServiceProvider',
    // 'Illuminate\Database\SeedServiceProvider',
]);
$app->command([
    // 'Kohkimakimoto\Luster\Commands\HelloCommand',
]);

$app->run();
```

Uncomment the line inside of `$app->command([...])` method.

```php
$app->command([
    'Kohkimakimoto\Luster\Commands\HelloCommand',
]);
```

WIP...

## Author

Kohki Makimoto <kohki.makimoto@gmail.com>

## License

MIT license.
