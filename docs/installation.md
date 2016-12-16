# Installation

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
