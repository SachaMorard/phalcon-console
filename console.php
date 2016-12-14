#!/usr/bin/env php
<?php

use Phalcon\Script;
use Phalcon\Script\Color;
use Phalcon\Devtools\Version;
use Phalcon\Commands\Builtin\Info;
use Phalcon\Commands\Builtin\Model;
use Phalcon\Commands\Builtin\Module;
use Phalcon\Commands\Builtin\Project;
use Phalcon\Commands\Builtin\Scaffold;
use Phalcon\Commands\CommandsListener;
use Phalcon\Commands\Builtin\Webtools;
use Phalcon\Commands\Builtin\AllModels;
use Phalcon\Commands\Builtin\Migration;
use Phalcon\Commands\Builtin\Enumerate;
use Phalcon\Commands\Builtin\Controller;
use Phalcon\Exception as PhalconException;
use Phalcon\Events\Manager as EventsManager;

try {
    require dirname(__FILE__) . '/bootstrap/autoload.php';

    $vendor = sprintf('Phalcon DevTools (%s)', Version::get());
    print PHP_EOL . Color::colorize($vendor, Color::FG_GREEN, Color::AT_BOLD) . PHP_EOL . PHP_EOL;

    $eventsManager = new EventsManager();

    $eventsManager->attach('command', new CommandsListener());

    $script = new Script($eventsManager);

    $commandsToEnable = [
        Info::class,
        Enumerate::class,
        Controller::class,
        Module::class,
        Model::class,
        AllModels::class,
        Project::class,
        Scaffold::class,
        Migration::class,
        Webtools::class,
    ];

    $script->loadUserScripts();

    foreach ($commandsToEnable as $command) {
        $script->attach(new $command($script, $eventsManager));
    }

    $script->run();
} catch (PhalconException $e) {
    fwrite(STDERR, Color::error($e->getMessage()) . PHP_EOL);
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, 'ERROR: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
