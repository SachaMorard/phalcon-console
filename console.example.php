#!/usr/bin/env php
<?php

use Phalcon\Script;
use Phalcon\Script\Color;
use Phalcon\Commands\CommandsListener;
use Phalcon\Exception as PhalconException;
use Phalcon\Events\Manager as EventsManager;

try {
    // Bellow, include your bootstrap, for example:
    require __DIR__ . '/app/bootstrap_cli.php';

    $eventsManager = new EventsManager();
    $eventsManager->attach('command', new CommandsListener());
    $script = new Script($eventsManager);
    $script->loadUserScripts();

    $commandsToEnable = $di->get('config')->commandsToEnable;
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
