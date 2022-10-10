<?php

namespace Core\Command;


class CommandHandler
{
    public static function resolve($args)
    {
        $name = $args[1];
        $args = array_slice($args, 2);
        $container = CommandContainer::instance();
        if($name == "--help"){
            echo "\n";
            echo "List command:\n";
            foreach($container->command as $k => $c){
                echo "\t$k\t=>\t".$c->{'description'}."\n";
            };
            echo "\n";
            exit();
        }
        else if (array_key_exists($name, $container->command)) {
            $ins =  $container->command[$name];
            if(sizeof($args) >0 && $args[0] == "--help"){
                echo "\n";
                echo "\t$name\t=>\t".$container->command[$name]->{'description'}."\n";
                echo "\n";
                exit();
            }
            $ins->handle($args);
        } else {
            echo "\n";
            echo "Command not found\n";
            exit();
        }
        return $ins;
    }
}
