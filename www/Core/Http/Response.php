<?php

namespace Core\Http;

use Core\Renderer\Template;
use Exception;

class Response{

    public  $twig;
    private static $HEADER ;
    private static  $_instance = null;
    protected $renderer ;

    public function __construct(){
        $this->renderer = new Template(APP_PATH."templates".DIRECTORY_SEPARATOR);
        $this->renderer->addFunction("uppercase",function($data){
            return strtolower($data);
        });
    }

    public static function getInstance() {
        if(is_null(self::$_instance)) {
          self::$_instance = new Response();  
        }
        return self::$_instance;
    }
    
    public static function Init(){
        $ins = self::getInstance();
        return $ins;
    }

    public static function  AddHeader($key,$value){
        self::$HEADER[$key] = $value;
    }

    public static function Redirect($name){
        $routes = Router::GetRoutes();
        if(array_key_exists($name,$routes['GET'])){
            $route = $routes['GET'][$name]->path;
            ob_start();
            header('Location: /'.$route);
            ob_end_flush();
            die();
        }
        else{
            throw new Exception('Route not found');
        } 
       
    }

    public static function RedirectToRoute($route="/"){
        ob_start();
        header('Location: '.$route);
        ob_end_flush();
        die();
    }

    public static function Json(array $data=[]){
        ob_start();
        header('Content-type:application/json;charset=utf-8');
        self::setHeader();
        echo json_encode($data);
        ob_end_flush();
        exit(200);
    }

    public static function setHeader(){
        if(self::$HEADER){
            foreach(self::$HEADER as $key=>$header)
                header($key.': '.$header);
        }
    }
    


    public static function Send(String $data=""){
        ob_start();
        header('Content-type:text/plain;charset=utf-8');
        self::setHeader();
        echo $data;
        ob_end_flush();
        exit(200);
    }

    public static function Render($template,$context=[]){
        ob_start();
        header('Content-type: text/html');
        self::setHeader();
        $ins = self::getInstance();
        $ins->renderer->view($template,$context);  
        ob_end_flush();
        exit(200);
    }



}
?>