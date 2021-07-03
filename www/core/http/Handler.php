<?php

namespace Core\Http;

use Exception;

class Handler {

    private static $_instance = null;

    public static function getInstance() {

        if(is_null(self::$_instance)) {
          self::$_instance = new Handler();  
        }
    
        return self::$_instance;
    }

    public function __construct()
    {
        $route =  $this->request_path();
        $this->path = "/".$route;
        Request::init($this->path);
        Response::init();
        Router::config($this->path);
        if(!file_exists(APP_PATH.'config/routes.php')){
            throw new Exception('routes.php file not found');
        }
        require APP_PATH.'config/routes.php';
    }

    public static function handle(){
        $ins = self::getInstance();
        $ins->doRouting();
    }


    public function doRouting(){
        Router::$isFound = false;
        $controller = Router::find();
        if(!Router::$isFound){
            return $controller->errorUrlNotFound();
        }
    }

    public  function request_path()
    {
        $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $script_name = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $parts = array_diff_assoc($request_uri, $script_name);
        if (empty($parts))
        {
            return '/';
        }
        $path = implode('/', $parts);
        if (($position = strpos($path, '?')) !== FALSE)
        {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    
}

?>