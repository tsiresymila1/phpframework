<?php
    namespace Middlewares;
    use Core\Http\CoreMiddlewares\Middleware;
use Core\Http\Response;
use Core\Session\Session;

class AuthMiddleware extends Middleware{
       
        public function before()
        {
            $key = Session::get("key");
            if(!isset($key)){
                Response::redirect("/login");
            }
        }
        public function after()
        {
             
        }
    }
?>