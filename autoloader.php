<?php

class Autoloader {

    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    static function autoload($class){
        $file = $class. '.php';
        $filepath =str_replace("\\",'/', DIR.$file);
        if(!file_exists( $filepath)){
            $filepath = str_replace("\\",'/',DIR."app\\".$file);
            if(!file_exists( $filepath)){
                $filepath = str_replace("\\",'/',DIR."libs\\".$file); ;
            }
        }
        include  $filepath;
    }

}

Autoloader::register(); 

?>