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
            session_start(
                [
                    'cookie_lifetime' => 86400,
                    'read_and_close'  => true
                ]
            );
        }

        public static function  set($key,$value){
            $_SESSION[ $key] = $value;
        }
        public static function  get($key){
            return isset($_SESSION[$key]) ?  $_SESSION[$key] : null;
        }
        public static function reset(){
            session_destroy();
        }

        public static function init(){
            // return self::getInstance();
        }

        
    }

?>