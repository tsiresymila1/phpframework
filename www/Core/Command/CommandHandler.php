<?php

namespace Core\Command;

use Core\Command\CommandContainer;

class CommandHandler
{
    public static function resolve($args)
    {
        $name = $args[1];
        $args = array_slice($args, 2);
        $container = CommandContainer::instance();
        if (array_key_exists($name, $container->command)) {
            $ins =  $container->command[$name];
            $ins->handle($args);
        } else {
            echo "Command not found\n";
                exit();
        }
        return $ins;
    }
}
