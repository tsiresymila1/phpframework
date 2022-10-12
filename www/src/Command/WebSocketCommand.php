<?php

namespace App\Command;

use Core\Command\Command;
use Workerman\Worker;
use Core\Http\WebSocket\WebSocketServer;
use App\Model\User;


class WebSocketCommand extends Command
{
    public $name =  "server:websocket";

    /**
     * @param $args
     */
    public function handle($args)
    {
        echo "\n\033[01;28mRunning socket server ...\n\033[0m";
        $this->startServer();
    }

    public function startServer($port=4445){
        $ws_server = new WebSocketServer(Worker::class,$port);
        $ws_server->On("connect",function($socket){
            $user = User::findOne(1);
            $socket->emit('message',$user);
        });
        $ws_server->On('message', function($socket,$data){
            $socket->emit('message',$data);
        });

        $ws_server->On('broadcast', function($socket,$data){
            $socket->broadcast($data);
        });
        $ws_server->start();
    }
}
