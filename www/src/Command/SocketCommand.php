<?php

namespace App\Command;

use Core\Command\Command;
use PHPSocketIO\SocketIO;
use Workerman\Worker;


class SocketCommand extends Command
{
    public $name =  "socket:run";

    /**
     * @param $args
     */
    public function handle($args)
    {
        echo "\nRunning socket server ...\n";
        $this->startServer();
    }

    public function startServer($port=4445){
        $io = new SocketIO($port);
        $io->on('connection', function ($socket) use ($io) {
            $socket->on('chat message', function ($msg) use ($io) {
                $io->emit('chat message', $msg);
            });
        });

        Worker::runAll();
    }
}
