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
    public array $parameters = [];
    public array $responses = [];
    public array $middlewares = [];
    protected static $prefix = [];
    protected static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Route();
        }
        return self::$_instance;
    }

    public  function cloneObject(){
        $new = new Route();
        $new->path = $this->path;
        $new->name = $this->name;
        $new->action = $this->action;
        $new->method = $this->method;
        $new->middlewares = $this->middlewares;
        $new->parameters = $this->parameters;
        $new->responses = $this->responses;
        $new->isAPI = $this->isAPI;
        return $new;
    }

    private function register()
    {
        $this->name = uniqid();
        $this->parameters = [];
        preg_replace_callback('/\\\\{([^}]*)\}+/', function ($match) {
            $exp = '/([a-zA-Z0-9\-\_]+)';
            if (strpos($match[1], '?') !== false) {
                $exp = '([/\\\\]{1,1}[a-zA-Z0-9\-\_]+)?';
                $param = str_replace('\\','',preg_replace('/[^A-Za-z0-9\-\/_]/','',$match[1]));
                $this->parameters[] = new OAIParameter($param, 'path', '', false);
            } else {
                $this->parameters[] = new OAIParameter(str_replace('\\','',$match[1]));
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
    private static function resolve($url, $action, $middlewares = null, $method = 'GET', $isAPI=null)
    {
        $ins = self::instance();
        $ins->action = $action;
        $ins->method = $method;
        $prev_isAPI = $ins->isAPI;
        if(!is_null($isAPI)){
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
    public static function Get($url, $action, $middlewares = null,$isAPI=null)
    {
        return self::resolve($url, $action, $middlewares, 'GET',$isAPI);
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @param null $isAPI
     * @return Route|null
     */
    public static function Post($url, $action, $middlewares = null,$isAPI=null)
    {
        return self::resolve($url, $action, $middlewares, 'POST',$isAPI);
    }

    /**
     * @param $url
     * @param $action
     * @param null $middlewares
     * @param null $isAPI
     * @return Route|null
     */
    public static function Any($url, $action, $middlewares = null,$isAPI=null)
    {
        return self::resolve($url, $action, $middlewares,'POST|GET', $isAPI);
    }

    /**
     * @param $url
     * @param null $middlewares
     * @param null $callback
     * @param bool $isAPI
     */
    public static function Group($url, $middlewares = null, $callback=null,$isAPI=null)
    {
        $ins = self::instance();
        $prev_middlewares = $ins->middlewares;
        $prev_suffix = self::$prefix;
        $prev_isAPI = $ins->isAPI;
        if(!is_null($isAPI)){
            $ins->isAPI = $isAPI;
        }
        self::$prefix[] = $url;
        if (gettype($middlewares) == "array") {
            $ins->middlewares = array_merge($ins->middlewares, $middlewares);
        } else {
            $ins->middlewares[] = $middlewares;
        }
        if(is_callable($callback)) $callback();
        self::$prefix = $prev_suffix;
        $ins->middlewares = $prev_middlewares;
        $ins->isAPI = $prev_isAPI;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name)
    {
        Router::Named($this->name, $name);
        $this->name = $name;
        return $this;
    }

    /**
     * @param $p
     * @return $this
     */
    public function addParameter($p)
    {
        if(gettype($p) == "array"){
            foreach ($p as $param){
                Router::AddParameter($this->name, $param);
            }
        }
        else{
            Router::AddParameter($this->name, $p);
        }
        return $this;
    }

    /**
     * @param $p
     * @return $this
     */
    public function addResponse($p)
    {
        Router::AddResponse($this->name, $p);
        return $this;
    }

}

?>