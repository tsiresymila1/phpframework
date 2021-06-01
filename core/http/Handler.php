<?php

namespace Core\Http;

use Exception;

class Handler {

    private static ?Handler $_instance = null;
    private array $routes;

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
        // load router configs
        if(!file_exists(DIR.'config/routes.php')){
            throw new Exception('File not found');
        }
        include DIR.'config/routes.php';
        $this->routes = $routes;
        //init router
        Request::init();
        Response::init();
        Router::config($this->path);
    }

    public static function handle(){
        $ins = self::getInstance();
        $ins->doRouting();
    }


    public function doRouting(){
        $controller = null;
        Router::$isFound = false;
        foreach($this->routes as  $url => $route) {
            $controller = Router::route($url,$route);
        }
        if(!Router::$isFound){
            var_dump( Router::$isFound);
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