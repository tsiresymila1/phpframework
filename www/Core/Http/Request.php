<?php

namespace Core\Http;

use Core\Utils\Logger;
use Utils\File;

class Request{

    protected static $_instance = null;
    protected  $method;
    protected  $path;
    protected  $get;
    protected  $post;
    protected  $files;
    protected  $params;
    protected  $headers;
    public static  $AJAX_HEADERS = [];
    protected $auth = false;

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
        $this->params = [];
        $this->headers = $this->getallheaders();
        foreach($_FILES as $key => $file){
            $this->files[$key] = new File($file);
        }
    }

    public function set($key,$value){
        $this->$key = $value;
    }

    public function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_' )
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public static function Init($path="/"){
        $ins = self::getInstance();
        $ins->set('path',$path);
        Logger::infos($ins ->method." ".$path,"REQUEST");
        return $ins;
    }

    public static function Get($key=null,$default=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->get[$key];
        }
        return $ins->get;
    }
    public static function Post($key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->post[$key];
        }
        return $ins->post;
    }

    public static function File($key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->file[$key];
        }
        return $ins->file;
    }

    public static function setParams($params){
        $ins = self::getInstance();
        $ins->set('params',$params);
        return $ins;
    }

    public static function Headers( $key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->headers[$key];
        }
        return $ins->headers;
    }

    public static function GetToken($key="Authorization"){
        $ins = self::getInstance();
        if(isset($ins->headers[$key])){
            return str_replace('Bearer ','',$ins->headers[$key]);
        }
        return '';
    }

    public static function Resources( $key=null){
        $ins = self::getInstance();
        if(!is_null($key)){
            return $ins->params[$key];
        }
        return $ins->params;
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
    public static function isAjax(){
        $ins = self::getInstance();
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /** 
    * @return Boolean
    */
    public static function isPost(){
        $ins = self::getInstance();
        return $ins->method === "POST";
    }
    /** 
    * @return Boolean
    */
    public static function isAuth(){
        $ins = self::getInstance();
        return $ins->auth;
    }

    /** 
    * @return String
    */
    public static  function getMethod(){
        $ins = self::getInstance();
        return $ins->method;
    }

    /** 
    * @return String
    */
    public static  function getPath(){
        $ins = self::getInstance();
        return $ins->path;
    }

    public static function redirect($route="/"){
        header('Location: '.$route);
        die();
    }

    public static function GetIP() {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

}
?>