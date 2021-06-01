<?php
    namespace Core\Session;
    
    class Session {

        private static ?Session $_instance = null;

        public static function getInstance() {
            if(is_null(self::$_instance)) {
            self::$_instance = new Session(); 
            }
            return self::$_instance;
        }

        public function __construct()
        {
            session_start();
        }

        public static function init(){
            return self::getInstance();
        }

        
    }

?>