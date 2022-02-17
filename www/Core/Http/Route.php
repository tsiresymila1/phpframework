<?php

    namespace Core\Http;
    use Core\Http\CoreControllers\Controller;
    class Route {

        public $path;
        public $name;
        public $action ;
        public $method ;
        public $middlewares = [];

        public function __construct()
        {
            $this->name = uniqid();
        }
        private static function instance(){
            return new Route();
        }

        public static function Get($url,$action,$middlewares=null){
            $ins = self::instance();
            $ins->path = trim($url,'/');
            $ins->action = $action;
            if(gettype($middlewares) == "array" ){
                $ins->middlewares = $middlewares;
            }
            else{
                $ins->middlewares = [$middlewares];
            }
            $ins->method = 'GET';
            $ins->register();
            return $ins;
        }

        public function register(){
            Router::Add($this,$this->name);
        }

        public static function Post($url,$action,$middlewares=null){
            $ins = self::instance();
            $ins->path = trim($url,'/');
            $ins->action = $action;
            $middlewares ??= [];
            if(gettype($middlewares) == "array" ){
                $ins->middlewares = $middlewares;
            }
            else{
                $ins->middlewares = [$middlewares];
            }
            $ins->method = 'POST';
            $ins->register();
            return $ins;
        }

        public static function Any($url,$action,$middlewares=null){
            $ins = self::instance();
            $ins->path = trim($url,'/');
            $ins->action = $action;
            $middlewares ??= [];
            if(gettype($middlewares) == "array" ){
                $ins->middlewares = $middlewares;
            }
            else{
                $ins->middlewares = [$middlewares];
            }
            $ins->method = 'POST|GET';
            $ins->register();
            return $ins;
        }

        public static function Group($url,$middlewares=null,$callback){
            $routes = call_user_func($callback);
            if($routes){      
                foreach($routes as $route){
                    $route->path = $url.'/'.$route->path;
                    $middlewares ??= [];
                    if(gettype($middlewares) !== "array" ){
                        $middlewares = [$middlewares];
                    }
                    $route->middlewares = array_merge($route->middlewares,$middlewares);
                    $route->register();
                }
            }
        }

        public function name($name){
            Router::Named( $this->name,$name);
        }

    }
?>