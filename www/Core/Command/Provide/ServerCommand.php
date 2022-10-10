<?php

namespace Core\Command\Provide;
use Core\Command\Command;

class ServerCommand extends Command
{
    public $name =  "serve";

    public $description = "Serve with php server";

    /**
     * @param $args
     */
    public function handle($args)
    {
        $port = sizeof($args) > 0 ? $args[0] : 4444;
        echo "\nStarting server on port ".$port." .....\n";
        $output = shell_exec("php -S localhost:".$port." -t ".DIR.DIRECTORY_SEPARATOR."/public");
    }
}
