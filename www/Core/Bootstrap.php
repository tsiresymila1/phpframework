<?php

    namespace Core;

    use Core\Database\DB;
    use Core\Http\Handler;
    use Core\Session\Session;
    use Core\Utils\Logger;

    class Bootstrap {
        public static function boot(){
            Session::Init();
            DB::init();
            Handler::handle();
            set_error_handler(function($errno, $errstr, $errfile, $errline ){
                Logger::error($errstr);
                exit();
            },E_ALL | E_STRICT | E_ERROR | E_WARNING);
            
        }
    }
?>