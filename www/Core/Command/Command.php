<?php

namespace Core\Command;

use LogicException;

abstract class Command
{
    public function __construct()
    {
        if (!isset($this->name))
            throw new LogicException(get_class($this) . ' must a command $name');
    }

    abstract public function handle($args);

    public function terminate()
    {
        echo "\n";
        flush();
        exit();
    }
}
