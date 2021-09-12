<?php

namespace Core\Http\CoreControllers;

use Core\Http\Response;

class Controller {

    public function __construct(){
    //    $this->request = Request::getInstance();
    //    $this->response = Response::getInstance();
    }

    public function index(){
        Response::send( static::class." was called ");
    }
    public function url404NotFound(){
        Response::send(" URL not found");
    }

}

?>