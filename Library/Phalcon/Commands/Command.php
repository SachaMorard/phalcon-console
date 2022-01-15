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
  |          Serghei Iakovlev <serghei@phalconphp.com>                     |
  +------------------------------------------------------------------------+
*/

namespace Phalcon\Commands;

use Phalcon\Script;
use Phalcon\Script\Color;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Filter;

/**
 * Command Class
 *
 * @package   Phalcon\Commands
 * @copyright Copyright (c) 2011-2016 Phalcon Team (team@phalconphp.com)
 * @license   New BSD License
 */
abstract class Command implements CommandsInterface
{
    /**
     * Script
     * @var \Phalcon\Script
     */
    protected $_script;

    /**
     * Events Manager
     * @var \Phalcon\Events\Manager
     */
    protected $_eventsManager;

    /**
     * Output encoding of the script.
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Parameters received by the script.
     * @var array
     */
    protected $_parameters = [];

    /**
     * Possible prepared arguments.
     * @var array
     */
    protected $_preparedArguments = [];

    /**
     * Dependency Injector
     *
     * @var \Phalcon\DiInterface
     */
    protected $_dependencyInjector;

    /**
     * Phalcon\Commands\Command
     *
     * @param \Phalcon\Script         $script
     * @param \Phalcon\Events\Manager $eventsManager
     */
    final public function __construct(Script $script, EventsManager $eventsManager)
    {
        $this->_script = $script;

        $this->_eventsManager = $eventsManager;
    }


    /**
     * @return \Phalcon\DiInterface
     */
    public function getDI()
    {
        return $this->_script->getDI();
    }

    /**
     * Events Manager
     *
     * @param \Phalcon\Events\Manager $eventsManager
     */
    public function setEventsManager(EventsManager $eventsManager)
    {
        $this->_eventsManager = $eventsManager;
    }

    /**
     * Returns the events manager
     *
     * @return \Phalcon\Events\Manager
     */
    public function getEventsManager()
    {
        return $this->_eventsManager;
    }

    /**
     * Sets the script that will execute the command
     *
     * @param \Phalcon\Script $script
     */
    public function setScript(Script $script)
    {
        $this->_script = $script;
    }

    /**
     * Returns the script that will execute the command
     *
     * @return \Phalcon\Script
     */
    public function getScript()
    {
        return $this->_script;
    }

    /**
     * @return \Phalcon\Config
     */
    protected function getConfig()
    {
        return $this->getDI()->get('config');
    }

    /**
     * Parse the parameters passed to the script.
     *
     * @param  array $parameters    Command parameters
     * @param  array $possibleAlias Command aliases
     * @return array
     *
     * @throws CommandsException
     *
     * @todo Refactor
     */
    public function parseParameters(array $parameters = [], $possibleAlias = [])
    {
        if (count($parameters) == 0) {
            $parameters = $this->getPossibleParams();
        }

        $arguments = [];
        foreach ($parameters as $parameter => $description) {
            if (strpos($parameter, "=") !== false) {
                $parameterParts = explode("=", $parameter);
                if (count($parameterParts) != 2) {
                    throw new CommandsException("Invalid definition for the parameter '$parameter'");
                }
                if (strlen($parameterParts[0]) == "") {
                    throw new CommandsException("Invalid definition for the parameter '$parameter'");
                }
                if (!in_array($parameterParts[1], ['s', 'i', 'l'])) {
                    throw new CommandsException("Incorrect data type on parameter '$parameter'");
                }
                $this->_preparedArguments[$parameterParts[0]] = true;
                $arguments[$parameterParts[0]] = [
                    'have-option' => true,
                    'option-required' => true,
                    'data-type' => $parameterParts[1]
                ];
            } else {
                if (!preg_match('/([a-zA-Z0-9]+)/', $parameter)) {
                    throw new CommandsException("Invalid parameter '$parameter'");
                }

                $this->_preparedArguments[$parameter] = true;
                $arguments[$parameter] = [
                    'have-option'     => false,
                    'option-required' => false
                ];
            }
        }

        $param = '';
        $paramName = '';
        $receivedParams = [];
        $i = 1;
        // ignore first arg which is 'console' or './console' and keep original indices
        $args = \array_slice($_SERVER['argv'], 1, \count($_SERVER['argv']) - 1, true);

        foreach ($args as $argv) {
            if (preg_match('#^([\-]{1,2})([a-zA-Z0-9][a-zA-Z0-9\-]*)(=(.*)){0,1}$#', $argv, $matches)) {
                if (strlen($matches[1]) == 1) {
                    $param = substr($matches[2], 1);
                    $paramName = substr($matches[2], 0, 1);
                } else {
                    if (strlen($matches[2]) < 2) {
                        throw new CommandsException("Invalid script parameter '$argv'");
                    }
                    $paramName = $matches[2];
                }

                if (!isset($this->_preparedArguments[$paramName])) {
                    if (!isset($possibleAlias[$paramName])) {
                        throw new CommandsException("Unknown parameter '$paramName'");
                    }
                    $paramName = $possibleAlias[$paramName];
                }

                if (isset($arguments[$paramName])) {
                    if ($param != '') {
                        $receivedParams[$paramName] = $param;
                        $param = '';
                        $paramName = '';
                    }
                    if ($arguments[$paramName]['have-option'] == false) {
                        $receivedParams[$paramName] = true;
                    } elseif (isset($matches[4])) {
                        $receivedParams[$paramName] = $matches[4];
                    }
                }
            } else {
                $param = $argv;
                if ($paramName != '') {
                    if (isset($arguments[$paramName])) {
                        if ($param == '') {
                            if ($arguments[$paramName]['have-option'] == true) {
                                throw new CommandsException("The parameter '$paramName' requires an option");
                            }
                        }
                    }
                    $receivedParams[$paramName] = $param;
                    $param = '';
                    $paramName = '';
                } else {
                    $receivedParams[$i - 1] = $param;
                    $i++;
                    $param = '';
                }
            }
        }

        $this->_parameters = $receivedParams;

        return $receivedParams;
    }

    /**
     * Check that a set of parameters has been received.
     *
     * @param $required
     *
     * @throws CommandsException
     */
    public function checkRequired($required)
    {
        foreach ($required as $fieldRequired) {
            if (!isset($this->_parameters[$fieldRequired])) {
                throw new CommandsException("The parameter '$fieldRequired' is required for this script");
            }
        }
    }

    /**
     * Sets the output encoding of the script.
     * @param $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->_encoding = $encoding;

        return $this;
    }

    /**
     * Displays help for the script.
     *
     * @param array $possibleParameters
     */
    public function showHelp($possibleParameters)
    {
        echo get_class($this).' - Usage:'.PHP_EOL.PHP_EOL;
        foreach ($possibleParameters as $parameter => $description) {
            echo html_entity_decode($description, ENT_COMPAT, $this->_encoding).PHP_EOL;
        }
    }

    /**
     * Returns all received options.
     *
     * @param mixed $filters Filter name or array of filters [Optional]
     *
     * @return array
     */
    public function getOptions($filters = null)
    {
        if (!$filters) {
            return $this->_parameters;
        }

        $result = [];

        foreach ($this->_parameters as $param) {
            $result[] = $this->filter($param, $filters);
        }

        return $result;
    }

    /**
     * Returns the value of an option received.
     *
     * @param mixed $option Option name or array of options
     * @param mixed $filters Filter name or array of filters [Optional]
     * @param mixed $defaultValue Default value [Optional]
     *
     * @return mixed
     */
    public function getOption($option, $filters = null, $defaultValue = null)
    {
        if (is_array($option)) {
            foreach ($option as $optionItem) {
                if (isset($this->_parameters[$optionItem])) {
                    if ($filters !== null) {
                        return $this->filter($this->_parameters[$optionItem], $filters);
                    }

                    return $this->_parameters[$optionItem];
                }
            }

            return $defaultValue;
        }

        if (isset($this->_parameters[$option])) {
            if ($filters !== null) {
                return $this->filter($this->_parameters[$option], $filters);
            }

            return $this->_parameters[$option];
        }

        return $defaultValue;
    }

    /**
     * Indicates whether the script was a particular option.
     *
     * @param  string  $option
     * @return boolean
     */
    public function isReceivedOption($option)
    {
        if (!is_array($option)) {
            $option = [$option];
        }

        foreach ($option as $op) {
            if (isset($this->_parameters[$op])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filters a value
     *
     * @param $paramValue
     * @param $filters
     *
     * @return mixed
     */
    protected function filter($paramValue, $filters)
    {
        $filter = new Filter();

        return $filter->sanitize($paramValue, $filters);
    }

    /**
     * Gets the last parameter is not associated with any parameter name.
     *
     * @return string
     */
    public function getLastUnNamedParam()
    {
        foreach (array_reverse($this->_parameters) as $key => $value) {
            if (is_numeric($key)) {
                return $value;
            }
        }

        return false;
    }

    /**
     * Checks if exists a certain unnamed parameter
     *
     * @param $number
     *
     * @return bool
     */
    public function isSetUnNamedParam($number)
    {
        return isset($this->_parameters[$number]);
    }

    /**
     * Prints the available options in the script
     *
     * @param array $parameters
     */
    public function printParameters($parameters)
    {
        $length = 0;
        foreach ($parameters as $parameter => $description) {
            if ($length == 0) {
                $length = strlen($parameter);
            }
            if (strlen($parameter) > $length) {
                $length = strlen($parameter);
            }
        }

        print Color::head('Options:') . PHP_EOL;
        foreach ($parameters as $parameter => $description) {
            print Color::colorize(' --' . $parameter . str_repeat(' ', $length - strlen($parameter)), Color::FG_GREEN);
            print Color::colorize("    " . $description) . PHP_EOL;
        }
    }

    /**
     * Returns the processed parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function canBeExternal()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $identifier
     *
     * @return boolean
     */
    public function hasIdentifier($identifier)
    {
        return in_array($identifier, $this->getCommands(), true);
    }
}
