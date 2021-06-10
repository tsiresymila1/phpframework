<?php

namespace Core\Http;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Response{

    public Environment $twig;
    private static ?Response $_instance = null;

    public function __construct(){
        $loader = new FilesystemLoader(DIR.'templates');
        $this->twig = new Environment($loader);
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

    public static function redirect($route="/"){
        header('Location: '.$route);
        die();
    }

    public static function json(array $data=[]){
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data);
    }


    public static function send(String $data=""){
        header('Content-type:text/plain;charset=utf-8');
        echo $data;
    }

    public static function render($template,$context=[]){
        $ins = self::getInstance();
        header('Content-type: text/html');
        echo $ins->twig->render($template,$context);
    }



}
?>