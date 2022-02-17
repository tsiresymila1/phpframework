<?php

use App\Command\HelloCommand;
use Core\Command\Provide\ControllerCommand;

return [
    ControllerCommand::class,
    // provide your custom command here
    HelloCommand::class
];
