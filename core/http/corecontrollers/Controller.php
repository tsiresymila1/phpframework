<?php

namespace Core\Http\CoreControllers;

use Core\Http\Request;
use Core\Http\Response;

class Controller {

    protected Request $request ;
    protected Response $response ;

    public function __construct(){
       $this->request = Request::getInstance();
       $this->response = Response::getInstance();
    }

    public function index(){
        $this->response->send( static::class." was called ");
    }
    public function errorUrlNotFound(){
        $this->response->send(" URL not found");
    }

    
}

?>