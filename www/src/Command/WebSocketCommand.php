<?php

namespace App\Command;

use Core\Command\Command;
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
        $this->startServer();
    }
    
    /**
     * startServer
     *
     * @param  mixed $port
     * @return void
     */
    public function startServer($port=4445){
        $ws_server = new WebSocketServer($port);
        $ws_server->On("connect",function($socket){
            $user = User::findOne(1);
            $socket->emit('message',$user);
        });
        $ws_server->On('message', function($socket,$data){
            $socket->emit('message',$data);
        });

        $ws_server->On('broadcast', function($socket,$data){
            $socket->emit($data);
        });
        $ws_server->start();
    }
}
