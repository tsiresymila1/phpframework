<?php

namespace App\Command;

use Core\Command\Command;
use Core\Database\Migration;

class MigrationCommand extends Command
{
    public $name =  "provide:migration";

    /**
     * @param $args
     */
    public function handle($args)
    {
        $m = new Migration();
        echo "\n";
    }
}
