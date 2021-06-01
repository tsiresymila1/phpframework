<?php

namespace Core\Http;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Response {

    private $twig;
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
        echo json_encode($data);
    }

    public static function send(String $data=""){
        echo $data;
    }

    public static function render(String $template, array $mixed = []){
        $ins = self::getInstance();
        echo  $ins->twig->render($template,$mixed);
    }

}
?>