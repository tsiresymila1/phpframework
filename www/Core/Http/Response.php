<?php

namespace Core\Http;

use BadMethodCallException;
use Core\Container\Container;
use Core\Renderer\Template;
use Exception;

class Response
{

    private static $HEADER;
    /**
     * @var static $_instance
     */
    private static  $_instance = null;
    public static $renderer;

    public function __construct()
    {
        self::$renderer = new Template(APP_PATH . "templates" . DIRECTORY_SEPARATOR);
        self::$renderer->addFunction("uppercase", function ($data) {
            return strtolower($data);
        });
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Response();
        }
        return self::$_instance;
    }

    /**
     * Init
     *
     * @return $_instance
     */
    public static function Init()
    {
        $container = Container::instance();
        $_i = self::instance();
        $container->register(static::class, static::class);
        return $_i;
    }

    /**
     * AddHeader
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public static function  AddHeader($key, $value)
    {
        self::$HEADER[$key] = $value;
    }

    public static function Redirect($name)
    {
        $routes = Router::GetRoutes();
        if (array_key_exists($name, $routes['GET'])) {
            $route = $routes['GET'][$name]->path;
            ob_start();
            header('Location: /' . $route);
            ob_end_flush();
            die();
        } else {
            throw new BadMethodCallException('Route not found');
        }
    }

    public static function RedirectToRoute($route = "/")
    {
        ob_start();
        header('Location: ' . $route);
        ob_end_flush();
        die();
    }

    /**
     * Json
     *
     * @param array $data = []
     *
     * @return void
     */
    public static function Json(array $data = [])
    {
        ob_start();
        header('Content-type:application/json;charset=utf-8');
        self::setHeader();
        echo json_encode($data);
        ob_end_flush();
        exit(200);
    }

    public static function setHeader()
    {
        if (self::$HEADER) {
            foreach (self::$HEADER as $key => $header)
                header($key . ': ' . $header);
        }
    }

    public static function Send(String $data = "")
    {
        ob_start();
        header('Content-type:text/plain;charset=utf-8');
        self::setHeader();
        echo $data;
        ob_end_flush();
        exit(200);
    }

    public static function Render($template, $context = [])
    {
        ob_start();
        header('Content-type: text/html');
        self::setHeader();
        self::$renderer->view($template, $context);
        ob_end_flush();
        exit(200);
    }
}
