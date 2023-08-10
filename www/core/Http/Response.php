<?php

namespace Core\Http;

use Core\Container\Container;
use Core\Renderer\Template2;
use Core\Debugbar\Debugbar;
use Core\Utils\Vite;
use Exception;

class Response
{

    private static array $HEADER = [];
    /**
     * @var static $_instance
     */
    private static $_instance = null;
    public static $template;
    private $content = null;
    private $status = 200;
    private $type = 200;
    public static $isRendered = false;

    public function __construct()
    {
        self::$template = new Template2(APP_PATH . "templates" . DIRECTORY_SEPARATOR);
        self::$template->addFunction("uppercase", function ($data) {
            return strtolower($data);
        });
        self::$template->addFunction("vite", function ($entry) {
            return Vite::vite($entry);
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

    private function setContentType($type = "text/html")
    {
        $this->type = $type;
    }

    public function getContentType()
    {
        return $this->type;
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
    public static function AddHeader($key, $value)
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
    public function setHeader($key, $value)
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
            $content = ob_get_contents();
            ob_end_clean();
            $ins = self::instance();
            $ins->setContent($content);
            $ins->setStatus(302);
            return $ins;
        } else {
            throw new Exception('Route not found', 404);
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
        $debugBar = Request::Headers('With-Debugbar');
        Response::instance()->setContentType('application/json;charset=utf-8');
        ob_start();
        header('Content-type:application/json;charset=utf-8');
        self::loadHeader();
        if (DEBUG && !is_null($debugBar)) {
            Debugbar::setResponse(Response::instance()->getStatus(), $data, Response::instance()->getContentType());
            $ins = Debugbar::load();
            $rep = ['debugbar' => $ins->logs, 'response' => $data];
            echo json_encode($rep);
        } else {
            echo json_encode($data);
        }
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
    public static function Send(string $data = "", $status = 200,$showdeBug= true)
    {
        Response::instance()->setContentType('text/plain;charset=utf-8');
        ob_start();
        header('Content-type:text/plain;charset=utf-8');
        self::loadHeader();
        echo $data;
        if (DEBUG && $showdeBug) {
            Debugbar::setResponse(Response::instance()->getStatus(), $data, Response::instance()->getContentType());
            Debugbar::show();
        }
        $content = ob_get_contents();
        ob_end_clean();
        $ins = self::instance();
        $ins->setContent($content);
        $ins->setStatus($status);
        return $ins;
    }

    public static function View($template, $context = [])
    {
        $template = str_replace('.', '/', $template);
        self::$isRendered = true;
        Response::instance()->setContentType();
        ob_start();
        header('Content-type: text/html');
        self::loadHeader();
        self::$template->render($template, $context);
        if (DEBUG) {
            Debugbar::setResponse(Response::instance()->getStatus(), $context, Response::instance()->getContentType());
            Debugbar::show();
        }
        $content = ob_get_contents();
        ob_end_clean();
        $ins = self::instance();
        $ins->setContent($content);
        $ins->setStatus(200);
        return $ins;
    }

    public static function Download($filename, $asAttachment, $headers = [])
    {
        if (file_exists($filename)) {
            header('Content-Description: Download file');
            header("Expires: 0");
            header('Pragma: public');
            if ($asAttachment) {
                header('Content-Type: application/octet-stream');
                header("Cache-Control: no-cache, must-revalidate");
                header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
            } else {

                $mime = mime_content_type($filename);
                if ($mime) {
                    header("Content-Type: {$mime}");
                } else {
                    header('Content-Type: application/octet-stream');
                }
                header('Content-Disposition: inline; filename="' . basename($filename) . '"');
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate');
            }
            header('Content-Length: ' . filesize($filename));
            foreach ($headers as $header) {
                header($header);
            }
            ob_clean();
            flush();
            readfile($filename);
            exit();
        } else {
            throw new Exception("File does not exist.");
        }
    }
}