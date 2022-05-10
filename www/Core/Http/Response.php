<?php

namespace Core\Http;

use Core\Container\Container;
use Core\Renderer\Template;
use Exception;

class Response
{

    private static array $HEADER = [];
    /**
     * @var static $_instance
     */
    private static  $_instance = null;
    public static $renderer;
    private $content = null;
    private $status = 200;

    public function __construct()
    {
        self::$renderer = new Template(APP_PATH . "templates" . DIRECTORY_SEPARATOR);
        self::$renderer->addFunction("uppercase", function ($data) {
            return strtolower($data);
        });
//        self::$HEADER['Access-Control-Allow-Origin'] = '*';
//        self::$HEADER['Access-Control-Allow-Methods'] = 'GET, POST, PUT';
//        self::$HEADER['Access-Control-Allow-Headers'] = 'Content-type';
    }

    private function setStatus($status = 200)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
    private function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
    public function getHeader()
    {
        return self::$HEADER;
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
    public static function loadHeader()
    {
        if (self::$HEADER) {
            foreach (self::$HEADER as $key => $header)
                header($key . ': ' . $header);
        }
    }
    public function setHeader($key,$value){
        self::$HEADER[$key] = $value;
    }

    public static function Redirect($name)
    {
        $routes = Router::GetRoutes();
        if (array_key_exists($name, $routes['GET'])) {
            $route = $routes['GET'][$name]->path;
            ob_start();
            header('Location: /' . $route);
            $content = ob_get_contents();
            ob_end_clean();
            $ins = self::instance();
            $ins->setContent($content);
            $ins->setStatus(302);
            return $ins;
        } else {
            throw new Exception('Route not found',404);
        }
    }
    
    /**
     * RedirectToRoute
     *
     * @param mixed route
     *
     * @return Response
     */
    public static function RedirectToRoute($route = "/")
    {
        ob_start();
        header('Location: ' . $route);
        $content = ob_get_contents();
        ob_end_clean();
        $ins = self::instance();
        $ins->setContent($content);
        $ins->setStatus(302);
        return $ins;
    }

    /**
     * Json
     *
     * @param array $data = []
     *
     * @return Response
     */
    public static function Json($data = [], $status = 200)
    {
        ob_start();
        header('Content-type:application/json;charset=utf-8');
        self::loadHeader();
        echo json_encode($data);
        $content = ob_get_contents();
        ob_end_clean();
        $ins = self::instance();
        $ins->setContent($content);
        $ins->setStatus($status);
        return $ins;
    }

    
    
    /**
     * Send
     *
     * @param string data
     * @param mixed status
     *
     * @return Response
     */
    public static function Send(String $data = "",$status = 200)
    {
        ob_start();
        header('Content-type:text/plain;charset=utf-8');
        self::loadHeader();
        echo $data;
        $content = ob_get_contents();
        ob_end_clean();
        $ins = self::instance();
        $ins->setContent($content);
        $ins->setStatus($status);
        return $ins;
    }

    public static function Render($template, $context = [])
    {
        $template = str_replace('.', '/', $template);
        ob_start();
        header('Content-type: text/html');
        self::loadHeader();
        self::$renderer->view($template, $context);
        $content = ob_get_contents();
        ob_end_clean();
        $ins = self::instance();
        $ins->setContent($content);
        $ins->setStatus(200);
        return $ins;
    }
}
