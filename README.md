# Phalcon Commands

This components is based on [Phalcon Devtools](https://github.com/phalcon/phalcon-devtools) and provides a really cool console 
to migrate your databases based on your models metadatas. Also, you could add your own commands.

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

You'll see a new console.exemple.php script at the root of your project. Copy it and start to edit it if you want.
```bash
cp console.exemple.php console
```


## Usage

To get a list of available commands just execute following:

```bash
 ./console
```

This command should display something similar to:

```sh
Available commands:
  info             (alias of: i)
  commands         (alias of: list, enumerate)
  mig              (alias of: migration)
```

## Migration

After include your bootstrap properly in the console script (line 15), you would be abble to 
use the fabulous migration commands.

Migrations commands generate scripts with datetime versioning. In your configuration file, you must to define a config->application->migrationDir node. 
Then you have to manage your model metadatas with sachoo/phalcon-model-annotations

To know more about the power of Migrations, execute:
```bash
 ./console mig
```

This command should display something similar to:

```sh
Help:
  Migration Commands

Usage: Generate a Migration
  mig gen

Usage: Run all available Migrations
  mig run

Usage: Run just one migration up
  mig up

Usage: Run just one migration down
  mig down

Usage: Generate migration file with Diff beetween Models and your Databases
  mig diff

Usage: Show migration status
  mig status
```