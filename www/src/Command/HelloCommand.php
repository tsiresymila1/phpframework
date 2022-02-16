<?php

namespace App\Command;

use Core\Command\Command;

class HelloCommand extends Command
{
    public $name =  "say:hello";

    public function handle($args)
    {
        echo "Hello from command";
    }
}
