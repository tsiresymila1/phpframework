<?php

namespace Core\Http;
use Core\OpenAPI\OpenApi;
use Exception;

class Handler
{

    private static $_instance = null;
    private string $path;

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new Handler();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $route =  $this->request_path();
        $this->path = "/" . $route;
        Request::Init($this->path);
        Response::Init();
        Router::Config($this->path);
        if (!file_exists(APP_PATH . 'config/routes.php')) {
            throw new Exception('routes.php file not found');
        }
        require APP_PATH . 'config/routes.php';
        $spec = OpenApi::getSPec();
    }

    public static function handle()
    {
        $ins = self::instance();
        $ins->auth();
    }

    public function auth()
    {
        if (file_exists(APP_PATH . 'config/security.php')) {
            $security = require APP_PATH . 'config/security.php';
            define('SECRET', in_array('secret', $security) ? $security['secret'] : '7c32d31dbdd39f2111da0b1dea59e94f3ed715fd8cdf0ca3ecf354ca1a2e3e30');
            $Athenticator = $security['authenticator'];
            $autheticator = new $Athenticator();
            $autheticator->authenticate();
        } else {
            self::DoRouting();
        }
    }

    public static function DoRouting()
    {
        Router::$isFound = false;
        $response = Router::find();
        self::renderViewContent($response);
    }

    public static function renderViewContent(Response $response)
    {
        echo $response->getContent();
        exit($response->getStatus());
    }

    /**
     * @return string
     */
    public  function request_path()
    {
        $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $script_name = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $parts = array_diff_assoc($request_uri, $script_name);
        if (empty($parts)) {
            return '/';
        }
        $path = implode('/', $parts);
        if (($position = strpos($path, '?')) !== FALSE) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }
}
