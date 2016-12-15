<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Developer Tools                                                |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2016 Phalcon Team (https://www.phalconphp.com)      |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

namespace Phalcon\Commands\Builtin;

use Phalcon\Script\Color;
use Phalcon\Commands\Command;
use Phalcon\Migrations;

/**
 * Migration Command
 *
 * Generates/Run a migration
 *
 * @package Phalcon\Commands\Builtin
 */
class Migration extends Command
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'action=s'          => 'Generates a Migration [generate|run]',
            'version=s'         => "Version to migrate.",
            'migrations=s'      => 'Migrations directory',
            'directory=s'       => 'Directory where the project was created',
            'help'              => 'Shows this help [optional]',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function run(array $parameters)
    {
        $path = $this->isReceivedOption('directory') ? $this->getOption('directory') : '';
        $path = realpath($path) . DIRECTORY_SEPARATOR;

        if ($this->isReceivedOption('config')) {
            $config = $this->loadConfig($path . $this->getOption('config'));
        } else {
            $config = $this->getConfig($path);
        }

        if ($this->isReceivedOption('migrations')) {
            $migrationsDir = $path . $this->getOption('migrations');
        } elseif (isset($config['application']['migrationsDir'])) {
            $migrationsDir = $config['application']['migrationsDir'];
            if (!$this->path->isAbsolutePath($migrationsDir)) {
                $migrationsDir = $path . $migrationsDir;
            }
        } elseif (file_exists($path . 'app')) {
            $migrationsDir = $path . 'app/migrations';
        } elseif (file_exists($path . 'apps')) {
            $migrationsDir = $path . 'apps/migrations';
        } else {
            $migrationsDir = $path . 'migrations';
        }

        $action = $this->getOption(['action', 1]);
        $version = $this->getOption('version');

        $mig = new Migrations($config, $migrationsDir, $path);

        switch ($action) {
            case 'gen':
                $mig->generate();
                break;
            case 'run':
                $mig->run($version);
                break;
            case 'up':
                $mig->run('up');
                break;
            case 'down':
                $mig->run('down');
                break;
            case 'diff':
                $mig->diff();
                break;
            case 'status':
                $mig->status();
                break;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getCommands()
    {
        return ['mig', 'migration'];
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function getHelp()
    {
        print Color::head('Help:') . PHP_EOL;
        print Color::colorize('  Migration Commands') . PHP_EOL . PHP_EOL;

        print Color::head('Usage: Generate a Migration') . PHP_EOL;
        print Color::colorize('  mig gen', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        print Color::head('Usage: Run all available Migrations') . PHP_EOL;
        print Color::colorize('  mig run', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        print Color::head('Usage: Run just one migration up') . PHP_EOL;
        print Color::colorize('  mig up', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        print Color::head('Usage: Run just one migration down') . PHP_EOL;
        print Color::colorize('  mig down', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        print Color::head('Usage: Generate migration file with Diff beetween Models and your Databases') . PHP_EOL;
        print Color::colorize('  mig diff', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        print Color::head('Usage: Show migration status') . PHP_EOL;
        print Color::colorize('  mig status', Color::FG_GREEN) . PHP_EOL . PHP_EOL;

        print Color::head('Arguments:') . PHP_EOL;
        print Color::colorize('  help', Color::FG_GREEN);
        print Color::colorize("\tShows this help text") . PHP_EOL . PHP_EOL;

        $this->printParameters($this->getPossibleParams());
    }

    /**
     * {@inheritdoc}
     *
     * @return integer
     */
    public function getRequiredParams()
    {
        return 1;
    }
}
