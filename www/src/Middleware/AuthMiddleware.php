<?php
    namespace App\Middleware;

    use Core\Http\CoreMiddlewares\BaseAuthMiddleware;
    use Core\Http\Response;
    use Core\Session\Session;

    class AuthMiddleware extends BaseAuthMiddleware {
       
        public function handle()
        {
            // $key = Session::get("key");
            // if(!isset($key)){
            //     Response::redirect("/login");
            // }
        }

       
    }
?>