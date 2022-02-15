<?php

namespace Core\Http;

use Core\Container\Container;
use Core\Utils\Logger;
use Utils\File;

/**
 * Request
 */
class Request
{

    /**
     * @var static $_instance
     */
    protected static $_instance = null;
    protected  $method;
    protected  $path;
    protected  $get;
    protected  $post;
    protected  $files;
    protected  $params;
    protected  $headers;
    public static  $AJAX_HEADERS = [];
    protected $auth = false;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Request();
        }
        return self::$_instance;
    }
    /**
     * construct
     *
     * @return void
     */

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->get = $_GET;
        $this->post = $_POST;
        $this->params = [];
        $this->headers = $this->getallheaders();
        foreach ($_FILES as $key => $file) {
            $this->files[$key] = new File($file);
        }
    }

    /**
     * set
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * getallheaders
     *
     * @return array
     */
    public function getallheaders()
    {
        $heads = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $heads[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $heads;
    }

    /**
     * Init
     *
     * @param mixed $path="/"
     *
     * @return void
     */
    public static function Init($path = "/")
    {
        $ins = self::instance();
        $ins->set('path', $path);
        Logger::infos($ins->method . " " . $path, "REQUEST");
        $container = Container::instance();
        $container->register(static::class, static::class);
        return $ins;
    }

    /**
     * Get
     *
     * @param mixed $key=null
     * @param mixed $default=null
     *
     * @return void
     */
    public static function Get($key = null)
    {
        $ins = self::instance();
        if (!is_null($key)) {
            return $ins->get[$key];
        }
        return $ins->get;
    }
    /**
     * Post
     *
     * @param $key = null
     *
     * @return void
     */
    public static function Post($key = null)
    {
        $ins = self::instance();
        if (!is_null($key)) {
            return $ins->post[$key];
        }
        return $ins->post;
    }

    /**
     * File
     *
     * @param $key = null
     *
     * @return void
     */
    public static function File($key = null)
    {
        $ins = self::instance();
        if (!is_null($key)) {
            return $ins->file[$key];
        }
        return $ins->file;
    }

    /**
     * setParams
     *
     * @param mixed $params
     *
     * @return void
     */
    public static function setParams($params)
    {
        $ins = self::instance();
        $ins->set('params', $params);
        return $ins;
    }

    /**
     * Headers
     *
     * @param $key = null
     *
     * @return void
     */
    public static function Headers($key = null)
    {
        $ins = self::instance();
        if (!is_null($key)) {
            return $ins->headers[$key];
        }
        return $ins->headers;
    }

    /**
     * GetToken
     *
     * @param $key = "Authorization"
     *
     * @return string
     */
    public static function GetToken($key = "Authorization")
    {
        $ins = self::instance();
        if (isset($ins->headers[$key])) {
            return str_replace('Bearer ', '', $ins->headers[$key]);
        }
        return '';
    }

    /**
     * Resources
     *
     * @param $key = null
     *
     * @return mixed
     */
    public static function Resources($key = null)
    {
        $ins = self::instance();
        if (!is_null($key)) {
            return $ins->params[$key];
        }
        return $ins->params;
    }

    /** 
     * @return Boolean
     */
    public static function isGet()
    {
        $ins = self::instance();
        return $ins->method === "GET";
    }
    /** 
     * @return Boolean
     */
    public static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /** 
     * @return Boolean
     */
    public static function isPost()
    {
        $ins = self::instance();
        return $ins->method === "POST";
    }
    /** 
     * @return Boolean
     */
    public static function isAuth()
    {
        $ins = self::instance();
        return $ins->auth;
    }

    /** 
     * @return String
     */
    public static  function getMethod()
    {
        $ins = self::instance();
        return $ins->method;
    }

    /** 
     * @return String
     */
    public static  function getPath()
    {
        $ins = self::instance();
        return $ins->path;
    }

    public static function redirect($route = "/")
    {
        header('Location: ' . $route);
        die();
    }

    public static function GetIP()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }
}
