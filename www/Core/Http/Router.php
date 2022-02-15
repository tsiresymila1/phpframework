<?php

namespace Core\Http;

use App\Controller;
use App\Middleware;
use BadMethodCallException;
use Core\Container\Container;
use Core\Http\CoreControllers\Controller as CoreController;
use Core\Http\CoreMiddlewares\BaseAuthMiddleware;
use Exception;
use ReflectionMethod;
use ReflectionParameter;

class Router
{

    public $matches;
    public $params = [];
    public $variables = [];
    public static $isFound;
    public static $path;
    public static Route $current;
    public  $name;
    public $namespace = "App\Controller\\";
    public $container;
    private  static $routes = [
        "GET" => [],
        "POST" => []
    ];

    private static  $_instance = null;

    private static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Router();
        }
        self::$_instance = new  Router();
        return self::$_instance;
    }

    public static function Config(String $path)
    {
        $ins = self::instance();
        $ins::$path = trim($path, '/');
        return $ins;
    }

    public static function GetRoutes()
    {
        return self::$routes;
    }

    public static function Add(Route $route, $name = null)
    {
        $methods = explode('|', $route->method);
        foreach ($methods as $method) {
            if (!is_null($name)) {
                self::$routes[$method][$name]  = $route;
            } else {
                self::$routes[$method][$route->name]  = $route;
            }
        }
    }

    public static function Named($oldname, $newname)
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $name => $route) {
                if ($name == $oldname) {
                    unset(self::$routes[$method][$oldname]);
                    self::$routes[$method][$newname] = $route;
                }
            }
        }
    }

    public function matches(string $url)
    {
        $pathrepalced = preg_replace_callback('/\\\\{([^}]*)\}+/', function ($match) {
            $this->variables[] = str_replace('\\', '', $match[1]);
            return '([a-zA-Z0-9\-\_]+)';
        },  preg_quote($url));

        $pathToMatch = "@^" . $pathrepalced . "$@D";
        if (preg_match($pathToMatch, self::$path, $matches)) {
            $this->matches = $matches;
            array_shift($matches);
            $this->params = array_combine($this->variables, $matches);
            Request::setParams($this->params);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Controller
     */
    public static function find()
    {
        $method = Request::getMethod();
        $ins = self::instance();
        $ins->container = Container::instance();
        $routes = self::$routes[$method];
        $controller = new CoreController();
        foreach ($routes as $route) {
            if ($ins->matches(trim($route->path, '/'))) {
                $ins::$isFound = true;
                $ins::$current = $route;
                $controller = $ins->execute();
                break;
            }
        }
        return $controller;
    }

    public function getArguments($class, $method)
    {
        $arguments = [];
        $r = new ReflectionMethod($class, $method);
        $paramsmethod = $r->getParameters();
        foreach ($paramsmethod as $p) {
            $pname = $p->name;
            $type = $p->getType();
            if (is_null($type) && isset($this->params[$pname])) {
                $arguments[] = $this->params[$pname];
            } else {
                if (!is_null($type) && method_exists($type->getName(), 'getInstance')) {
                    $className = $type->getName();
                    $arguments[] = $className::instance();
                } else {
                    throw new BadMethodCallException('Error params not found ');
                }
            }
        }
        return $arguments;
    }

    public function invokeSucess()
    {
        $cparams = explode("@", self::$current->action);
        $ControllerClass = $this->namespace . $cparams[0];
        $method = $cparams[1];
        $ctrlins = $this->container->resolve($ControllerClass, $method);
        return  $ctrlins;
    }

    public function invokeFail($method)
    {
        $methodsparams = explode("@", $this->action);
        $ControllerClass = $this->namespace . $methodsparams[0];
        $ctrlins = $this->container->resolve($ControllerClass, $method);
        return  $ctrlins;
    }

    public function execute()
    {
        foreach (self::$current->middlewares as $middleware) {
            if (!is_null($middleware)) {
                $MiddlewareClass = "App\Middleware\\" . $middleware;
                $middleins = $this->container->make($MiddlewareClass);
                if ($middleins instanceof BaseAuthMiddleware) {
                    $middleins->handle();
                }
            }
        }
        return $this->invokeSucess();
    }
}
