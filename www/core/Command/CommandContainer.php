<?php

namespace Core\Command;

use Core\Command\Provide\CreateMigrationCommand;
use Core\Command\Provide\ControllerCommand;
use Core\Command\Provide\MigrateCommand;
use Core\Command\Provide\ServerCommand;
use Core\Container\Container;
use RuntimeException;

class CommandContainer
{
    public $command = [];
    protected static $instance;

    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }
    public function register($classname)
    {
        $ioc = Container::instance();
        if (property_exists($classname, 'name')) {
            $ins = $ioc->make($classname, []);
            $name = $ins->{'name'};
            $this->command[$name] = $ins;
        } else {
            echo ($classname . ' attribute name not found');
            exit();
        }
    }
    public static function Init()
    {
        if (!file_exists(APP_PATH . 'config/command.php')) {
            throw new RuntimeException('File not found');
        }
        $ins = self::instance();
        $command = require APP_PATH . 'config/command.php';
        $default = [
            ControllerCommand::class,
            ServerCommand::class,
            CreateMigrationCommand::class,
            MigrateCommand::class
        ];
        foreach (array_merge($default, $command) as $c) {
            $ins->register($c);
        }
    }
}