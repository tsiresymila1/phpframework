<?php

namespace Core\Http;

use Core\OpenAPI\OAIParameter;

class Route
{

    public $path;
    public $name;
    public $action;
    public $method;
    public $isAPI = false;
    public $isGroup = false;
    public array $parameters = [];
    public array $responses = [];
    public array $middlewares = [];
    protected static $prefix = [];
    protected static $_instance = null;
    protected $names = [];

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
        $new->isAPI = $this->isAPI;
        $new->names = $this->names;
        $new->isGroup = $this->isGroup;
        return $new;
    }

    private function register()
    {
        $this->name = uniqid();
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
     * @param null $isAPI
     * @return Route|null
     */
    private static function resolve($url, $action, $middlewares = null, $method = 'GET', $isAPI = null)
    {
        $ins = self::instance();
        $ins->isGroup = false;
        $ins->action = $action;
        $ins->method = $method;
        $prev_isAPI = $ins->isAPI;
        if (!is_null($isAPI)) {
            $ins->isAPI = $isAPI;
        }
        $middlewares ??= [];
        $prev_middlewares = $ins->middlewares;
        if (gettype($middlewares) == "array") {
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
        $ins->isAPI = $prev_isAPI;
        return $ins;
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @param null $isAPI
     * @return Route|null
     */
    public static function Get($url, $action, $middlewares = null, $isAPI = null)
    {
        return self::resolve($url, $action, $middlewares, 'GET', $isAPI);
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @param null $isAPI
     * @return Route|null
     */
    public static function Post($url, $action, $middlewares = null, $isAPI = null)
    {
        return self::resolve($url, $action, $middlewares, 'POST', $isAPI);
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @param null $isAPI
     * @return Route|null
     */
    public static function Any($url, $action, $middlewares = null, $isAPI = null)
    {
        return self::resolve($url, $action, $middlewares, 'POST|GET', $isAPI);
    }

    /**
     * @param $url
     * @param null $middlewares
     * @param null $callback
     * @param null $isAPI
     * @return Route|null
     */
    public static function Group($url, $middlewares = null, $callback = null, $isAPI = null)
    {
        $ins = self::instance();
        $prev_middlewares = $ins->middlewares;
        $prev_suffix = self::$prefix;
        $prev_isAPI = $ins->isAPI;
        $prev_parameters = $ins->parameters;
        $prev_responses = $ins->responses;
        $prev_names = $ins->names;
        $ins->names = [];
        $group_name = $ins->name;
        if (!is_null($isAPI)) {
            $ins->isAPI = $isAPI;
        }
        self::$prefix[] = $url;
        if (gettype($middlewares) == "array") {
            $ins->middlewares = array_merge($ins->middlewares, $middlewares);
        } else {
            $ins->middlewares[] = $middlewares;
        }
        $groupes_middlewares = $ins->middlewares;
        if (is_callable($callback)) $callback();
        $ins->middlewares = $groupes_middlewares;
        $ins->name = $group_name;
        $ins->isGroup = true;
        $clone = $ins->cloneObject();
        self::$prefix = $prev_suffix;
        $ins->middlewares = $prev_middlewares;
        $ins->isAPI = $prev_isAPI;
        $ins->parameters = $prev_parameters;
        $ins->responses = $prev_responses;
        $ins->names = array_merge($prev_names, $ins->names);
        return $clone;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        if ($this->isGroup) {
            foreach ($this->names as $n) {
                $new_name = $name . '_' . $n;
                Router::Named($n, $new_name);
                $this->names[array_search($n, $this->names)] = $new_name;
            }
        } else {
            Router::Named($this->name, $name);
            $this->names[array_search($this->name, $this->names)] = $name;
        }
        $this->name = $name;
        return $this;
    }

    /**
     * @param $p
     * @return $this
     */
    public function addParameter($p)
    {
        if (gettype($p) == "array") {
            if (!$this->isGroup) {
                $this->parameters = array_merge($this->parameters, $p);
            } else {
                foreach ($p as $param) {
                    foreach ($this->names as $name) {
                        Router::AddParameter($name, $param);
                    }
                }
            }
        } else {
            if (!$this->isGroup) {
                $this->parameters[] = $p;
            } else {
                foreach ($this->names as $name) {
                    Router::AddParameter($name, $p);
                }
            }
        }
        return $this;
    }

    /**
     * @param $r
     * @return $this
     */
    public function addResponse($r)
    {
        if (gettype($r) == "array") {
            if (!$this->isGroup) {
                $this->parameters = array_merge($this->parameters, $r);
            } else {
                foreach ($r as $param) {
                    foreach ($this->names as $name) {
                        Router::AddResponse($name, $param);
                    }
                }
            }
        } else {
            if (is_null($this->name)) {
                $this->parameters[] = $r;
            } else {
                foreach ($this->names as $name) {
                    Router::AddResponse($name, $r);
                }
            }
        }
        return $this;
    }

}

?>