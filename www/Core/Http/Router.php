<?php

namespace Core\Http;

use BadMethodCallException;
use Core\Container\Container;
use Core\Http\CoreControllers\Controller;
use Core\Http\CoreControllers\Controller as CoreController;
use Core\Http\CoreMiddlewares\BaseAuthMiddleware;
use Exception;
use ReflectionMethod;

class Router
{

    public $matches;
    public $params = [];
    public $variables = [];
    public static $isFound;
    public static $path;
    public static  $current;
    public  $name;
    public $namespace = "App\Controller\\";
    public $container;
    private  static $routes = [
        "GET" => [],
        "POST" => []
    ];

    private static  $_instance = null;

    /**
     * @return Router|null
     */
    private static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Router();
        }
        self::$_instance = new  Router();
        return self::$_instance;
    }

    /**
     * @param String $path
     * @return Router|null
     */
    public static function Config(String $path)
    {
        $ins = self::instance();
        $ins::$path = trim($path, '/');
        return $ins;
    }

    /**
     * @return array[]
     */
    public static function GetRoutes()
    {
        return self::$routes;
    }

    /**
     * @param $route
     * @param null $name
     */
    public static function Add($route, $name = null)
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

    /**
     * @param $oldname
     * @param $newname
     */
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

    /**
     * @param string $url
     * @return bool
     */
    public function matches(string $url)
    {
        $this->variables = [];
        $pathrepalced = preg_replace_callback('/\\\\{([^}]*)\}+/', function ($match) {
            $exp = '([a-zA-Z0-9\-\_]+)';
            if (strpos($match[1], '?') !== false) {
                $exp =  '([a-zA-Z0-9\-\_]+)?';
            }
            $this->variables[] = str_replace(['\\', '?'], '', $match[1]);
            return $exp;
        },  preg_quote($url));

        $pathToMatch = "@^" . $pathrepalced . "$@D";
        if (preg_match($pathToMatch, self::$path, $matches)) {
            $this->matches = $matches;
            array_shift($matches);
            while (sizeof($this->variables) > sizeof($matches)) {
                $matches[] = null;
            }
            $this->params = array_combine($this->variables, $matches);

            Request::setParams($this->params);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Response | null
     */
    public static function find()
    {
        $method = Request::getMethod();
        $ins = self::instance();
        $ins->container = Container::instance();
        $routes = self::$routes[$method];
        foreach ($routes as $route) {
            if (trim($route->path, '/') == "*" || $ins->matches(trim($route->path, '/'))) {
                $ins::$isFound = true;
                $ins::$current = $route;
                return $ins->execute();
                break;
            }
        }
        if(defined('DEBUG') && DEBUG == false) {
            $controller = new CoreController();
            return  $controller->url404NotFound();
        }
        else{
            throw new Exception('Route /'. self::$path.' not found', 404);
        }
        return null;
    }

    /**
     * invokeSuccess
     *
     * @return Controller
     */
    public function invokeSuccess()
    {
        $cparams = explode("@", self::$current->action);
        $ControllerClass = $this->namespace . $cparams[0];
        $method = $cparams[1];
        $content = $this->container->resolve($ControllerClass, $method, $this->params);
        return  $content;
    }

    /**
     * invokeFail
     *
     * @param mixed method
     *
     * @return Controller
     */
    public function invokeFail($method)
    {
        $methodsparams = explode("@", $this->action);
        $ControllerClass = $this->namespace . $methodsparams[0];
        $content = $this->container->resolve($ControllerClass, $method, $this->params);
        return  $content;
    }

    /**
     * execute
     *
     * @return Controller
     */
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
        return $this->invokeSuccess();
    }
    public static function renderViewContent($content, $status = 200)
    {
        echo $content;
        exit($status);
    }
}
