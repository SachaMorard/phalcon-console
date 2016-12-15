# Phalcon Commands

[![Latest Version](https://img.shields.io/packagist/v/sachoo/phalcon-console.svg?style=flat-square)][:console:]
[![Software License](https://img.shields.io/badge/license-BSD--3-brightgreen.svg?style=flat-square)][:license:]
[![Total Downloads](https://img.shields.io/packagist/dt/sachoo/phalcon-console.svg?style=flat-square)][:packagist:]
[![Daily Downloads](https://img.shields.io/packagist/dd/sachoo/phalcon-console.svg?style=flat-square)][:packagist:]

This components is based on [Phalcon Devtools](https://github.com/phalcon/phalcon-devtools) and provides a really cool console 
line tool to generate codes, and a powerful system to migrate your databases based on your models metadatas.

## Requirements

* PHP >= 5.5
* Phalcon >= 3.0.0

## Installing via Composer

Install composer in a common location or in your project:

```bash
curl -s http://getcomposer.org/installer | php
```

Create the composer.json file as follows:

```json
{
    "require-dev": {
        "sachoo/phalcon-console": "~1.*"
    }
}
```

Run the composer installer:

```bash
php composer.phar install
```

## Usage

To get a list of available commands just execute following:

```bash
 vendor/bin/console.php
```

This command should display something similar to:

```sh
$ phalcon --help

Phalcon Console (1.0.0)

Help:
  Lists the commands available in Console

Available commands:
  info             (alias of: i)
  commands         (alias of: list, enumerate)
  mig              (alias of: migration)
```
