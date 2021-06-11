<?php

    namespace Core\Http;
    use Controllers;
    use Core\Http\CoreControllers\Controller;
    use Middlewares;

    class Router {

        private static ?Router $_instance = null;
        private String $route;
        public static bool $isFound;

        public static function getInstance(){
            if(is_null(self::$_instance)) {
              self::$_instance = new Router();  
            }
            return self::$_instance;
        }

        public static function config(String $route){
            $ins = self::getInstance();
            $ins->route = $route;
            return $ins;
        }

        /**
         * @return Controller
         */
        public static function route(String $url, array $route){
            $pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($url)) . "$@D";
            if(self::isMatch($route['method'],$pattern)){
                if(isset($route['middlewares'])){
                    $middlewares = $route['middlewares'];
                    $controller = self::applyMiddleware($middlewares,$route['controller']);
                }
                else{
                    $controller = self::applyController($route['controller']);
                } 
                self::$isFound = true;
                 
            }
            else{
                $controller = new Controller();
            }
            return $controller;
        }

        /**
         * @return Controller
         */
        public static function applyMiddleware($middlewares,$controller){
            foreach($middlewares as $middleware){
                $MiddlewareClass = "Middlewares\\".$middleware;
                $middleins = new $MiddlewareClass;
                $middleins->handle();
            }
            $ctrlins = self::applyController($controller);
            foreach($middlewares as $middleware){
                $MiddlewareClass = "Middlewares\\".$middleware;
                $middleins = new $MiddlewareClass;
                $middleins->after();
            }
            foreach($middlewares as $middleware){
                $MiddlewareClass = "Middlewares\\".$middleware;
                $middleins = new $MiddlewareClass;
                $middleins->finish();
            }
            return $ctrlins;
        }

        /**
         * @return Controller
         */
        public static function applyController($controller){
            $controllers = explode("@",$controller);
            $ControllerClass = "Controllers\\".$controllers[0];
            $method = $controllers[1];
            $ctrlins = new $ControllerClass;
            $ctrlins->$method();
            return  $ctrlins ;
        }

        /**
         * @return Boolean
         */
        public static function isMatch(String $method,String $pattern){
            $ins = self::getInstance();
            $matches = Array();
            $methods = explode('|',$method);
            $route_method = Request::getMethod();
            if(in_array($route_method,$methods) && preg_match($pattern, $ins->route , $matches)) {
                return true;
            }
            return false;
        }
    }
?>