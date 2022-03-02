<?php

    namespace Core\Http;
    class Route {

        public $path;
        public $name;
        public $action ;
        public $method ;
        public $middlewares = [];
        public $prefix = [] ;
        protected static $_instance = null;

        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new Route();
            }
            return self::$_instance;
        }

        private function register(){
            $this->name = uniqid();
            Router::Add((object)(array)$this,$this->name);
        }
        private  static function resolve($url,$action,$middlewares=null, $method='GET'){
            $ins = self::instance();
            $ins->path = implode('', $ins->prefix).'/'.trim($url,'/');
            $ins->action = $action;
            $ins->method = $method;
            $middlewares ??= [];
            if(gettype($middlewares) == "array" ){
                $ins->middlewares = array_merge($ins->middlewares,$middlewares);
            }
            else{
                $ins->middlewares[] = $middlewares;
            }
            $ins->register();
            $array_middlewares =  $ins->middlewares;
            if(gettype($middlewares) == "array" ){
                foreach($middlewares as $m){
                    array_pop($array_middlewares);
                }
            }
            else{
                array_pop($array_middlewares);
            }
            $ins->middlewares = $array_middlewares;
            return $ins;
        }

        public static function Get($url,$action,$middlewares=null){
            return self::resolve($url,$action,$middlewares=null);
        }

        public static function Post($url,$action,$middlewares=null){
            return self::resolve($url,$action,$middlewares=null,'POST');
        }

        public static function Any($url,$action,$middlewares=null){
            return self::resolve($url,$action,$middlewares=null,'POST|GET');
        }

        public static function Group($url,$middlewares=null,$callback){
            $ins = self::instance();
            $ins->prefix[] = $url;
            if(gettype($middlewares) == "array" ){
                $ins->middlewares = array_merge($ins->middlewares,$middlewares);
            }
            else{
                $ins->middlewares[] = $middlewares;
            }
            $callback();
            array_pop($ins->prefix);
            $array_middlewares =  $ins->middlewares;
            if(gettype($middlewares) == "array" ){
                foreach($middlewares as $m){
                    array_pop($array_middlewares);
                }
            }
            else{
                array_pop($array_middlewares);
            }
            $ins->middlewares = $array_middlewares;
        }

        public function name($name){
            Router::Named( $this->name,$name);
        }

    }
?>