<?php

namespace Core\Http\CoreMiddlewares;

use Core\Http\Request;

class Middleware {

    private Request $request;

    public function __construct()
    {
        $request = Request::getInstance();
    }

    public function before(){
        
    }

    public function after(){
        
    }

    public function finish(){
        
    }

    
}
?>