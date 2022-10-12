<?php

namespace Core\Http\WebSocket;

use Exception;

class WebSocketServer {

    private $ws_worker;
    private $worker_class;
    private $events = [];
    private $clients = [];


    public function __construct($worker,$port=4445)
    {
        $this->ws_worker = new $worker("websocket://0.0.0.0:$port");
        $this->worker_class = $worker;
    }

    private function add($key, $value)
    {
        $this->events[$key] = $value;
    }

    public function get($key)
    {
        if(array_key_exists($key, $this->events)){ 
            return $this->events[$key];
        }
        else{
            return null;
        }
    }

    public function start(){
        if($this->ws_worker != null){
            try{
                $this->ws_worker->onConnect = function ($connection) {
                    $this->clients[$connection->id] = $connection;
                    $callback = $this->get('connect');
                    if($callback){
                        call_user_func_array($callback,[new WebSocketInstance($connection,$this->clients)]);
                    }
                };
        
                $this->ws_worker->onMessage = function ($connection, $message) {
                    echo "Message => $message\n";
                    $json_data = (array)json_decode($message);
                    if(is_array($json_data) && key_exists('event',$json_data) && key_exists('data',$json_data)){
                        $event = $json_data['event'];
                        $data = $json_data['data'];
                        $callback = $this->get($event);
                        if($callback){
                            call_user_func_array($callback,[new WebSocketInstance($connection,$this->clients), $data]);
                        }
                    }
                };
        
                $this->ws_worker->onClose = function ($connection) {
                    $callback = $this->get('disconnect');
                    unset($this->clients[$connection->id]);
                    if($callback){
                        call_user_func($callback,[new WebSocketInstance($connection,$this->clients)]);
                    }
                    echo "Connection closed\n";
                };
                $this->worker_class::runAll();
            }
            catch(Exception $e){
                $message = $e->getMessage();
                echo "\033[01;28mError on starting websocket server $message \033[0m";
            }
        }
    }

    public function On($event,$callback){
        $this->add($event,$callback);
    }

    public function broadcast($message){
        foreach($this->clients as $client){
            $client->send('broadcast',$message);
        }
    }

}

class WebSocketInstance {
    public $connection ;
    public $clients ;

    public function __construct($connection,$clients = [])
    {
        $this->connection = $connection;
        $this->clients = $clients;
    }

    public function emit($event,$data){
        $message = ["event"=>$event, "data"=>$data];
        $this->connection->send(json_encode($message));
    }

    public function broadcast($data){
        $message = ["event"=>'broadcast', "data"=>$data];
        foreach($this->clients as $client){
            $client->send('broadcast',$message);
        }
    }
}