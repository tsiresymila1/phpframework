<?php

class Autoloader {

    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    static function autoload($class){
        $file = $class. '.php';
        $filepath =str_replace("\\",'/', DIR.$file);
        if(!file_exists( $filepath)){
            $file = "libs\\".$file ;
        }
        include  $file;
    }

}

Autoloader::register(); 

?>