<?php

namespace Core\Http;

use Core\Container\Container;
use Core\Http\CoreControllers\Controller;
use Core\Http\CoreControllers\Controller as CoreController;
use Core\Http\CoreMiddlewares\BaseAuthMiddleware;
use Core\OpenAPI\OAIParameter;
use Core\OpenAPI\OAIResponse;
use Exception;

class Router
{

    public $matches;
    public $params = [];
    public $variables = [];
    public static $isFound;
    public static $path;
    public static $current;
    public $name;
    public $namespace = "App\Controller\\";
    public $container;
    private static $routes = [
        "GET" => [],
        "POST" => []
    ];

    private static $_instance = null;

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
    public static function Config(string $path)
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
                self::$routes[$method][$name] = $route;
            } else {
                self::$routes[$method][$route->name] = $route;
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
                    break;
                }
            }
        }
    }

    /**
     * @param $oldname
     * @param OAIParameter $p
     */
    public static function AddParameter($oldname, OAIParameter $p)
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $name => $route) {
                if ($name == $oldname) {
                    $route->parameters[] = $p;
                    self::$routes[$method][$oldname] = $route;
                    break;
                }
            }
        }
    }

    /**
     * @param $search_name
     * @param OAIResponse $r
     */
    public static function AddResponse($search_name, OAIResponse $r)
    {
        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $name => $route) {
                if ($name == $search_name) {
                    $route->response[] = $r;
                    self::$routes[$method][$search_name] = $route;
                    break;
                }
            }
        }
    }

    /**
     * @param $callable
     * @return bool
     */
    public function isFunction($callable)
    {
        return $callable && !is_string($callable) && !is_array($callable) && is_callable($callable);
    }

    /**
     * @param string $url
     * @return bool
     */
    public function matches(string $url)
    {
        $this->variables = [];
        $pathrepalced = preg_replace_callback('/\/\\\\{([^}]*)\}+/', function ($match) {
            $exp = '/([a-zA-Z0-9\-\_]+)';
            if (strpos($match[1], '?') !== false) {
                $exp = '([/\\\\]{1,1}[a-zA-Z0-9\-\_]+)?';
            }
            $this->variables[] = str_replace(['\\', '?'], '', $match[1]);
            return $exp;
        }, preg_quote($url));

        $pathToMatch = "@^" . $pathrepalced . "$@D";
        $tomatch = self::$path;
        if (preg_match($pathToMatch, $tomatch, $matches)) {
            $this->matches = $matches;
            array_shift($matches);
            $matches = array_map(function ($m) {
                $p = ltrim($m, '/');
                $ps = explode('/', $p);
                return $ps[0];
            }, $matches);
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
     * @return Response
     * @throws Exception
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
        if (defined('DEBUG') && DEBUG == false) {
            $controller = new CoreController();
            return $controller->url404NotFound();
        } else {
            throw new Exception('Route /' . self::$path . ' not found', 404);
        }
    }

    /**
     * invokeSuccess
     *
     * @return Controller
     */
    public function invokeSuccess()
    {
        if ($this->isFunction(self::$current->action)) {
            return $this->container->resolve(self::$current->action, null, $this->params, true);
        } else {
            $cparams = explode("@", self::$current->action);
            $ControllerClass = $this->namespace . $cparams[0];
            $method = $cparams[1];
            return $this->container->resolve($ControllerClass, $method, $this->params);
        }

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
                if ($this->isFunction($middleware)) {
                    $this->container->resolve($middleware, null, $this->params, true);
                } else {
                    $MiddlewareClass = "App\Middleware\\" . $middleware;
                    $middleins = $this->container->make($MiddlewareClass, [], $this->params);
                    if ($middleins instanceof BaseAuthMiddleware) {
                        $middleins->handle();
                    }
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
