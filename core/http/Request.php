<?php

namespace Core\Http;

use Core\Utils\Logger;

class Request{

    private static ?Request $_instance = null;
    private String $method;
    private array $get;
    private array $post;
    private array $files;

    public static function getInstance() {
        if(is_null(self::$_instance)) {
          self::$_instance = new Request();  
        }
        return self::$_instance;
    }

    public function __construct(){
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
    }

    public static function init($path="/"){
        $ins = self::getInstance();
        Logger::infos($ins ->method." ".$path,"REQUEST");
        return $ins;
    }

    public static function get(String $key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->get[$key];
        }
        return $ins->get;
    }
    public static function post(String $key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->post[$key];
        }
        return $ins->post;
    }

    public static function file(String $key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->file[$key];
        }
        return $ins->file;
    }

    /** 
    * @return Boolean
    */
    public static function isGet(){
        $ins = self::getInstance();
        return $ins->method === "GET";
    }
    /** 
    * @return Boolean
    */
    public static function isPost(){
        $ins = self::getInstance();
        return $ins->method === "POST";
    }

    /** 
    * @return String
    */
    public static  function getMethod(){
        $ins = self::getInstance();
        return $ins->method;
    }

    public static function redirect($route="/"){
        header('Location: '.$route);
        die();
    }

}
?>