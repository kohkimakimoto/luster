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

Run a `luster init` to create your command line app project files.

```
php vendor/bin/luster init
```

You will get some directories and files. Look at `bin/cmd`. It is the executable command file to bootstrap the app. You should rename it.

Run this command.

```
php bin/cmd
```

WIP...

## Author

Kohki Makimoto <kohki.makimoto@gmail.com>

## License

MIT license.
