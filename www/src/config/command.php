<?php

use App\Command\HelloCommand;
use App\Command\MigrationCommand;
use App\Command\SocketCommand;
use App\Command\WebSocketCommand;
return [
    HelloCommand::class,
    MigrationCommand::class,
    SocketCommand::class,
    WebSocketCommand::class
];
