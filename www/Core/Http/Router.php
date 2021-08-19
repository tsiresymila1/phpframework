<?php

    namespace Core\Http;
    use Core\Http\CoreControllers\Controller;
    class Router {

        private static  $_instance = null;
        public static $isFound;
        public $routes = [
            "GET"=>[],
            "POST" => []
        ];

        private static function getInstance(){
            if(is_null(self::$_instance)) {
              self::$_instance = new Router();  
            }
            return self::$_instance;
        }

        public function getRoute(){
            return $this->routes;
        }

        public static function Config(String $route){
            $ins = self::getInstance();
            $ins->route = $route;
            return $ins;
        }

        public static function Get($url,$action,$middlewares=null){
            $ins = self::getInstance();
            $route = new Route($url,$action,$middlewares);
            $ins->routes['GET'][] = $route ;
            return $ins;
        }

        public static function Post($url,$action,$middlewares=null){
            $ins = self::getInstance();
            $route = new Route($url,$action,$middlewares);
            $ins->routes['POST'][] = $route ;
            return $ins;
        }

        public static function All($url,$action,$middlewares=null){
            $ins = self::getInstance();
            $route = new Route($url,$action,$middlewares);
            $ins->routes['POST'][] = $route;
            $ins->routes['GET'][] = $route;
            return $ins;
        }

        /**
         * @return Controller
         */
        public static function find(){
            $method = Request::getMethod();
            $ins = self::getInstance();
            $routes = $ins->getRoute()[$method];
            $controller = new Controller() ;
            foreach($routes as $route){
                if($route->matches($ins->route)){
                    $ins::$isFound = true;
                    $controller = $route->execute();
                    break;
                }
            }
            return $controller;
        }
    }
?>