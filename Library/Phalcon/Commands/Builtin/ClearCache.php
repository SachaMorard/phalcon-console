<?php

namespace Phalcon\Commands\Builtin;

use Phalcon\Script\Color;
use Phalcon\Commands\Command;

/**
 * Class ClearCache
 * @package Phalcon\Commands\Builtin
 */
class ClearCache extends Command
{

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getPossibleParams()
    {
        return [
            'help' => 'Shows this help [optional]',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param array $parameters
     * @return mixed
     */
    public function run(array $parameters)
    {
        print Color::colorize('Clear File Cache:', Color::FG_BROWN) . PHP_EOL;
        $cacheDir = $this->getConfig()->application->cacheDir;
        $directories = scandir($cacheDir);
        foreach ($directories as $dir) {
            if ($dir !== "." && $dir !== ".." && $dir !== ".gitignore") {
                print Color::colorize('    Empty cache for ' . $dir, Color::FG_LIGHT_PURPLE) . PHP_EOL;


                $thisCacheDir = $cacheDir . $dir . '/';
                $files = scandir($thisCacheDir);
                foreach ($files as $file) {
                    if ($file !== "." && $file !== ".." && $file !== ".gitignore") {
                        unlink($thisCacheDir . $file);
                        print Color::colorize('     Erase ' . $thisCacheDir . $file, Color::FG_LIGHT_GRAY, Color::AT_ITALIC) . PHP_EOL;
                    }
                }
            }
        }
        print PHP_EOL;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getCommands()
    {
        return ['cc', 'clear-cache'];
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function canBeExternal()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function getHelp()
    {
        print Color::head('Help:') . PHP_EOL;
        print Color::colorize('  Clear file cache in var folder') . PHP_EOL . PHP_EOL;

        $this->run([]);
    }

    /**
     * {@inheritdoc}
     *
     * @return integer
     */
    public function getRequiredParams()
    {
        return 0;
    }
}
