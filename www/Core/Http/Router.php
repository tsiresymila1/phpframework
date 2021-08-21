<?php

    namespace Core\Http;
    use App\Controller;
    use App\Middleware;
use Core\Http\CoreControllers\Controller as CoreController;
use Core\Http\CoreMiddlewares\BaseAuthMiddleware;
    use Exception;
    use ReflectionMethod;
    use ReflectionParameter;

    class Router {

        public $matches ;
        public $params = [];
        public $variables = [];
        public static $isFound;
        public static $path;
        public static Route $current;
        public  $name;
        private  static $routes = [
            "GET"=>[],
            "POST" => []
        ];

        private static  $_instance = null;
        
        private static function getInstance(){
            if(is_null(self::$_instance)) {
              self::$_instance = new Router();  
            }
            self::$_instance = new  Router();
            return self::$_instance;
        }

        public static function Config(String $path){
            $ins = self::getInstance();
            $ins::$path = trim($path,'/');
            return $ins;
        }

        public static function GetRoutes(){
            return self::$routes;
        }

        public static function Add(Route $route,$name=null){
            $methods = explode('|',$route->method);
            foreach($methods as $method){
                if(!is_null($name)){
                    self::$routes[$method][$name]  = $route;
                }
                else{
                    self::$routes[$method][$route->name]  = $route;
                }
                
            }
        }

        public static function Named($oldname,$newname){
            foreach(self::$routes as $method => $routes){
                foreach($routes as $name => $route){
                    if($name == $oldname){
                        unset(self::$routes[$method][$oldname]);
                        self::$routes[$method][$newname] = $route;
                    }
                }
            }
        }

        public function matches(string $url){
            $pathrepalced = preg_replace_callback('/\\\\{([^}]*)\}+/', function($match){
                $this->variables[] = str_replace('\\','',$match[1]);
                return '([a-zA-Z0-9\-\_]+)';
            },  preg_quote($url));

            $pathToMatch = "@^" . $pathrepalced . "$@D";
            if(preg_match($pathToMatch,self::$path,$matches)){
                $this->matches = $matches;
                array_shift($matches);
                $this->params = array_combine($this->variables,$matches);
                Request::setParams($this->params);
                return true;
            }
            else{
                return false;
            }
        }

        /**
         * @return Controller
         */
        public static function find(){
            $method = Request::getMethod();
            $ins = self::getInstance();
            $routes = self::$routes[$method];
            $controller = new CoreController() ;
            foreach($routes as $route){
                if($ins->matches(trim($route->path,'/'))){
                    $ins::$isFound = true;
                    $ins::$current = $route;
                    $controller = $ins->execute();
                    break;
                }
            }
            return $controller;
        }

        public function getArguments($class,$method){
            $arguments = [];
            $r = new ReflectionMethod($class, $method);
            $paramsmethod = $r->getParameters();
            foreach($paramsmethod as $p){
                $name = $p->name;
                if(isset($this->params[$name])){
                    $arguments[] = $this->params[$name];
                }
                else{   
                    $type = $p->getType();
                    if(!is_null($type) && method_exists($type->getName(),'getInstance')){
                        $className = $type->getName();
                        $arguments[] = $className::getInstance();
                    }
                    else throw new Exception('Error params not found ');
                } 
            }
            return $arguments;
        }

        public function invokeSucess(){
            $params = explode("@",self::$current->action);
            $ControllerClass = "App\Controller\\".$params[0];
            $method = $params[1];
            $arguments = $this->getArguments($ControllerClass,$method);
            $ctrlins = new $ControllerClass();
            $ctrlins->$method(...$arguments);
            return  $ctrlins ;
        }

        public function invokeFail($method){
            $params = explode("@",$this->action);
            $ControllerClass = "App\Controller\\".$params[0];
            $arguments = $this->getArguments($ControllerClass,$method);
            $ctrlins = new $ControllerClass();
            $ctrlins->$method(...$arguments);
            return  $ctrlins ;
        }

        public function execute(){
            foreach(self::$current->middlewares as $middleware){
                if(!is_null($middleware)){ 
                    $MiddlewareClass = "App\Middleware\\".$middleware;
                    $middleins = new $MiddlewareClass();
                    if($middleins instanceof BaseAuthMiddleware){
                        $middleins->handle();
                    }
                    
                }
            }
            $ctrlins = $this->invokeSucess();
            return $ctrlins;
        }
    }


?>