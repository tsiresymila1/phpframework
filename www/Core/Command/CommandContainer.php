<?php

namespace Core\Command;

use Core\Container\Container;
use Exception;
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
    public  function register($classname)
    {
        $ioc = Container::instance();
        if (property_exists($classname, 'name')) {
            $ins = $ioc->make($classname, []);
            $name = $ins->{'name'};
            $this->command[$name] = $ins;
        } else {
            throw new Exception('Command attribute name not found');
        }
    }
    public static  function Init()
    {
        if (!file_exists(APP_PATH . 'config/command.php')) {
            throw new RuntimeException('File not found');
        }
        $ins = self::instance();
        $command = require APP_PATH . 'config/command.php';
        foreach ($command as $c) {
            $ins->register($c);
        }
    }
}
