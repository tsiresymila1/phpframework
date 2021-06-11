<?php
    namespace Middlewares;
    use Core\Http\CoreMiddlewares\Middleware;
    use Core\Http\Response;
    use Core\Session\Session;

    class AuthMiddleware implements Middleware {
       
        public function handle()
        {
            $key = Session::get("key");
            if(!isset($key)){
                Response::redirect("/login");
            }
        }
    }
?>