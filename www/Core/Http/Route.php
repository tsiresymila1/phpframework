<?php

    namespace Core\Http;
    use App\Controller;
    use App\Middleware;
    use Core\Http\CoreMiddlewares\BaseAuthMiddleware;
    use Exception;
    use ReflectionMethod;
    use ReflectionParameter;

    class Route {

        public $path;
        public $action ;
        public $matches ;
        public $middlewares = [];
        public $params = [];
        public $variables = [];

        public function __construct($path,$action,$middleware=null)
        {
            $this->path = trim($path,'/');
            $this->action = $action;
            if(gettype($middleware) == "array"){
                $this->middlewares = $middleware;
            }
            else{
                $this->middlewares = [$middleware];
            }
        }

        public function matches(string $url){
            $url = trim($url,'/');
            $pathrepalced = preg_replace_callback('/\\\\{([^}]*)\}+/', function($match){
                $this->variables[] = str_replace('\\','',$match[1]);
                return '([a-zA-Z0-9\-\_]+)';
            },  preg_quote($this->path));

            $pathToMatch = "@^" . $pathrepalced . "$@D";
            if(preg_match($pathToMatch,$url,$matches)){
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
            $params = explode("@",$this->action);
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
            foreach($this->middlewares as $middleware){
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