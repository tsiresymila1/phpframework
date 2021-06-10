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

    public static function get(String $key){
        $ins = self::getInstance();
        if(isset($ins->get[$key])){
            return $ins->get[$key];
        }
        return null;
    }
    public static function post(String $key){
        $ins = self::getInstance();
        if(isset($ins->post[$key])){
            return $ins->post[$key];
        }
        return null;
    }

    public static function file(String $key){
        $ins = self::getInstance();
        if(isset($ins->files[$key])){
            return $ins->post[$key];
        }
        return null;
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

}
?>