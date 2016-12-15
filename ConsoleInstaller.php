<?php
namespace Phalcon;
use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class ConsoleInstaller implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var IOInterface
     */
    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_PACKAGE_INSTALL => [
                ['copyConsole', 0]
            ],
            ScriptEvents::POST_PACKAGE_UPDATE => [
                ['askForUpdate', 0]
            ],
            ScriptEvents::POST_PACKAGE_UNINSTALL => [
                ['deleteConsole', 0]
            ]
        ];
    }

    public static function copyConsoleToRoot(Event $event)
    {
        $event->getIO()->write("<warning>Console is now a Composer Plugin and installs console.exemple.php automatically.</warning>");
        $event->getIO()->write("<warning>Please remove current \"post-install-cmd\" and \"post-update-cmd\" hooks from your composer.json</warning>");
    }

    public function copyConsole()
    {
        if ($this->ConsoleNotChanged()) {
            $this->io->write("<comment>[sachoo/phalcon-console]</comment> console.exemple.php is already up-to-date");
            return;
        }

        $this->io->write("<comment>[sachoo/phalcon-console]</comment> Copying console.exemple.php to the root of your project...");
        copy(__DIR__ . DIRECTORY_SEPARATOR . 'console.exemple.php', getcwd() . DIRECTORY_SEPARATOR.'console.exemple.php');
    }

    public function askForUpdate()
    {
        if ($this->ConsoleNotChanged()) {
            return;
        }
        if (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'console.exemple.php')) {
            $replace = $this->io->askConfirmation("<warning>console.exemple.php has changed</warning> Do you want to replace console.exemple.php with latest version?", false);
            if (!$replace) {
                return;
            }
        }
        $this->copyConsole();
    }

    private function ConsoleNotChanged()
    {
        return file_exists(getcwd() . DIRECTORY_SEPARATOR . 'console.exemple.php') &&
            md5_file(__DIR__ . DIRECTORY_SEPARATOR . 'console.exemple.php') === md5_file(getcwd() . DIRECTORY_SEPARATOR . 'console.exemple.php');
    }

    public function deleteConsole()
    {
        if (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'console.exemple.php')) {
            $this->io->write("<comment>[sachoo/phalcon-console]</comment> Deleting console.exemple.php from the root of your project...");
            unlink(getcwd() . DIRECTORY_SEPARATOR . 'console.exemple.php');
        }
    }
}

