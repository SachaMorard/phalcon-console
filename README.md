# Phalcon Console

This components is based on 
[Phalcon Devtools](https://github.com/phalcon/phalcon-devtools) 
and provides a really cool console 
to migrate your databases based on your models metadatas. 
Also, you could add your own commands.

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
    "require": {
        "sachoo/phalcon-console": "~1.*"
    }
}
```

Run the composer installer:

```bash
php composer.phar install
```

If it not exists yet, create a console binary script at the root of your project. 
If you need an example, you can copy console.example.php from sachoo/phalcon-console 
component:
```bash
cp vendor/sachoo/phalcon-console/console.example.php console.php
```

To enable the commands you want, those you've wrote, and those from community, 
you have to add a new array on your config:
```php
'commandsToEnable' => [
    \Phalcon\Commands\Builtin\Info::class,
    \Phalcon\Commands\Builtin\Enumerate::class,
    \Phalcon\Commands\Builtin\Migration::class,
    \Phalcon\Commands\Builtin\ClearCache::class,
    \Commands\MyCommand::class
 ],
```

Also, you have to include your boostrap on line 12 properly
```php
require __DIR__ . '/app/bootstrap_cli.php';
```

## Usage

To get a list of available commands just execute following:

```bash
 ./console.php
```

This command should display something similar to:

```sh
Available commands:
  info             (alias of: i)
  commands         (alias of: list, enumerate)
  mig              (alias of: migration)
  cc               (alias of: clear-cache)
```

## Migration

Migrations commands generate scripts with datetime versioning. 
In your configuration file, you must to define a 
config->application->migrationDir value. 
Then you have to manage your model metadatas with 
[sachoo/phalcon-model-annotations](https://github.com/sachoo/phalcon-model-annotations)

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