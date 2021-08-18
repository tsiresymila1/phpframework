<?php

namespace Core\Http;

use Core\Renderer\Template;

class Response{

    public  $twig;
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
    
    public static function init(){
        $ins = self::getInstance();
        return $ins;
    }

    public static function Redirect($route="/"){
        ob_start();
        header('Location: '.$route);
        ob_end_flush();
        die();
    }

    public static function Json(array $data=[]){
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data);
    }


    public static function Send(String $data=""){
        // header('Content-type:text/plain;charset=utf-8');
        echo $data;
    }

    public static function Render($template,$context=[]){
        header('Content-type: text/html');
        $ins = self::getInstance();
        $ins->renderer->view($template,$context);
    }



}
?>