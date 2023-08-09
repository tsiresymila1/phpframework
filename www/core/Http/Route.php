<?php

namespace Core\Http;

use Core\OpenAPI\OAIParameter;
use Core\OpenAPI\OAIRequestBody;
use Core\OpenAPI\OAIResponse;

class Route
{

    public $path;
    public $name;
    public $action;
    public $method;
    public $isAPI = null;
    public $isGroup = false;
    public array $parameters = [];

    public ?OAIRequestBody $requestBody = null;
    public array $responses = [];
    public array $security = [];
    public array $middlewares = [];
    protected static $prefix = [];
    protected static $_instance = null;
    public array $group_names = [];
    public array $names = [];

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Route();
        }
        return self::$_instance;
    }

    public function cloneObject()
    {
        $new = new Route();
        $new->path = $this->path;
        $new->name = $this->name;
        $new->action = $this->action;
        $new->method = $this->method;
        $new->middlewares = $this->middlewares;
        $new->parameters = $this->parameters;
        $new->responses = $this->responses;
        $new->security = $this->security;
        $new->isAPI = $this->isAPI;
        $new->group_names = $this->group_names;
        $new->isGroup = $this->isGroup;
        return $new;
    }

    private function register()
    {
        $this->name = uniqid();
        $this->group_names[] = $this->name;
        $this->names[] = $this->name;
        $this->parameters = [];
        preg_replace_callback('/\\\\{([^}]*)\}+/', function ($match) {
            $exp = '/([a-zA-Z0-9\-\_]+)';
            if (strpos($match[1], '?') !== false) {
                $exp = '([/\\\\]{1,1}[a-zA-Z0-9\-\_]+)?';
                $param = str_replace('\\', '', preg_replace('/[^A-Za-z0-9\-\/_]/', '', $match[1]));
                $this->parameters[] = new OAIParameter($param, 'path', '', false);
            } else {
                $this->parameters[] = new OAIParameter(str_replace('\\', '', $match[1]), 'path');
            }

            return $exp;
        }, preg_quote($this->path));
        Router::Add($this->cloneObject(), $this->name);
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @param string $method
     * @return Route|null
     */
    private static function resolve($url, $action, $middlewares = null, $method = 'GET')
    {
        $ins = self::instance();
        $ins->isGroup = false;
        $ins->action = $action;
        $ins->method = $method;
        $ins->names = [];
        $middlewares ??= [];
        $prev_middlewares = $ins->middlewares;
        if (is_array($middlewares)) {
            $ins->middlewares = array_merge($ins->middlewares, $middlewares);
        } else {
            $ins->middlewares[] = $middlewares;
        }
        if (!is_array($url)) {
            $url = [$url];
        }
        foreach ($url as $r) {
            $ins->path = implode('', self::$prefix) . '/' . trim($r, '/');
            $ins->register();
        }
        $ins->middlewares = $prev_middlewares;
        $ins->isAPI = null;
        return $ins;
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @return Route|null
     */
    public static function Get($url, $action, $middlewares = null)
    {
        return self::resolve($url, $action, $middlewares, 'GET');
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @return Route|null
     */
    public static function Post($url, $action, $middlewares = null)
    {
        return self::resolve($url, $action, $middlewares, 'POST');
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @return Route|null
     */
    public static function Delete($url, $action, $middlewares = null)
    {
        return self::resolve($url, $action, $middlewares, 'DELETE');
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @return Route|null
     */
    public static function Put($url, $action, $middlewares = null)
    {
        return self::resolve($url, $action, $middlewares, 'PUT');
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @return Route|null
     */
    public static function Any($url, $action, $middlewares = null)
    {
        return self::resolve($url, $action, $middlewares, 'POST|GET|DELETE|PUT');
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @return Route|null
     */
    public static function Ressource($url, $action, $middlewares = null)
    {
        self::resolve($url, $action.'@show', $middlewares, 'GET');
        self::resolve($url, $action.'@store', $middlewares, 'POST');
        self::resolve($url."{id}", $action.'@delete', $middlewares, 'DELETE');
        self::resolve($url.'{id}', $action.'@update', $middlewares, 'PUT');
    }


    /**
     * @param $url
     * @param null $callback
     * @param null $middlewares
     * @return Route
     */
    public static function Group($url, $callback = null, $middlewares = null)
    {
        $ins = self::instance();
        $prev_middlewares = $ins->middlewares;
        $prev_suffix = self::$prefix;
        $prev_isAPI = $ins->isAPI;
        $prev_parameters = $ins->parameters;
        $prev_responses = $ins->responses;
        $prev_group_names = $ins->group_names;
        $ins->group_names = [];
        $group_name = $ins->name;
        self::$prefix[] = $url;
        if (gettype($middlewares) == "array") {
            $ins->middlewares = array_merge($ins->middlewares, $middlewares);
        } else {
            $ins->middlewares[] = $middlewares;
        }
        $group_middlewares = $ins->middlewares;
        if (is_callable($callback)) $callback();
        $ins->middlewares = $group_middlewares;
        $ins->name = $group_name;
        $ins->isGroup = true;
        $clone = $ins->cloneObject();
        self::$prefix = $prev_suffix;
        $ins->middlewares = $prev_middlewares;
        $ins->isAPI = $prev_isAPI;
        $ins->parameters = $prev_parameters;
        $ins->responses = $prev_responses;
        $ins->group_names = array_merge($prev_group_names, $ins->group_names);
        return $clone;
    }

    public function asApi($isApi = true)
    {
        if ($this->isGroup) {
            foreach ($this->group_names as $n) {
                Router::asAPI($n, $isApi);
            }
        } else {
            foreach ($this->names as $n) {
                Router::asAPI($n, $isApi);
            }
        }
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        if ($this->isGroup) {
            foreach ($this->group_names as $n) {
                $new_name = $name . '_' . $n;
                Router::Named($n, $new_name);
                $this->group_names[array_search($n, $this->group_names)] = $new_name;
            }
            $this->name = $name;
        } else {
            $isMore = sizeof($this->names)>1;
            foreach ($this->names as $key=>$n) {
                $new_name = $isMore ? $name.(intval($key +1)) : $name;
                Router::Named($n,$new_name);
                $this->group_names[array_search($n, $this->group_names)] = $new_name;
                $this->names[array_search($n, $this->names)] = $new_name;
                $this->name = $new_name;
            }
        }
        return $this;
    }

    /**
     * @param  $p
     * @return $this
     */
    public function middleware($p)
    {
        if (!$this->isGroup) {
            foreach ($this->names as $n) {
                Router::AddMiddleware($n, $p);
            }
        } else {
            foreach ($this->group_names as $name) {
                Router::AddMiddleware($name, $p);
            }
        }
        return $this;
    }

    /**
     * @param array|OAIParameter $p
     * @return $this
     */
    public function addOAIParameter($p)
    {
        if (!$this->isGroup) {
            foreach ($this->names as $n) {
                Router::AddParameter($n, $p);
            }
        } else {
            foreach ($this->group_names as $name) {
                Router::AddParameter($name, $p);
            }
        }
        return $this;
    }

    /**
     * @param array|OAIResponse $r
     * @return Route
     */
    public function addOAIResponse($r)
    {
        if (!$this->isGroup) {
            foreach ($this->names as $n) {
                Router::AddResponse($n, $r);
            }
        } else {
            foreach ($this->group_names as $name) {
                Router::AddResponse($name, $r);
            }
        }
        return $this;
    }

     /**
     * @param (array  | OAIResponse) $r
     * @return $this
     */
    public function addOAISecurity($r)
    {
        if (!$this->isGroup) {
            foreach ($this->names as $n) {
                Router::AddSecurity($n, $r);
            }
        } else {
            foreach ($this->group_names as $name) {
                Router::AddSecurity($name, $r);
            }
        }
        return $this;
    }

     /**
     * @param array|OAIRequestBody $p
     * @return $this
     */
    public function addOAIRequestBody($b)
    {
        if (!$this->isGroup) {
            foreach ($this->names as $n) {
                Router::AddRequestBody($n, $b);
            }
        } else {
            foreach ($this->group_names as $name) {
                Router::AddRequestBody($name, $b);
            }
        }
        return $this;
    }

}

?>