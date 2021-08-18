<?php
    namespace Core\Session;
    
    class Session {

        private static $_instance = null;

        public static function getInstance() {
            if(is_null(self::$_instance)) {
            self::$_instance = new Session(); 
            }
            return self::$_instance;
        }

        public function __construct()
        {
            ini_set('session.save_path',realpath(DIR.'/storage/session/'));
            ini_set('session.cookie_domain', $_SERVER['HTTP_HOST']);
            session_start();
        }

        public static function  Set($key,$value){
            if (isset($_SESSION[$key])) unset($_SESSION[$key]);
            $_SESSION[$key] = $value;
        }
        public static function  Get($key,$default=null){
            return isset($_SESSION[$key]) ?  $_SESSION[$key] : $default;
        }
        public static function Reset(){
            session_destroy();
        }
        public static function Remove($key=null){
            if($key != null && in_array($key,$_SESSION)){
                unset($_SESSION[$key]);
            }
        }

        public static function Init(){
            return self::getInstance();
        }

        
    }

?>