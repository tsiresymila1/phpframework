<?php

    namespace Core;

    use Core\Database\DB;
    use Core\Http\Handler;
    use Core\Session\Session;
    use Core\Utils\Logger;
    use Exception;

    class Bootstrap {
        public static function boot(){
            Session::Init();
            DB::init();
            Handler::handle();
            set_error_handler(function($errno, $errstr, $errfile, $errline ){
                Logger::error($errno);
                Logger::error($errstr);
                Logger::error($errfile);
                Logger::error($errline);
                exit(500);
            },E_ALL | E_STRICT | E_ERROR | E_WARNING | E_NOTICE);

            set_exception_handler(function(Exception $e)
            {   
                $errors = array(
                    E_USER_ERROR        => "User Error",
                    E_USER_WARNING      => "User Warning",
                    E_USER_NOTICE       => "User Notice",
                );
                Logger::error($errors[$e->getCode()].': '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
                Logger::error($e->getTraceAsString());
            });

            register_shutdown_function(function () {
                $err = error_get_last();
                if (! is_null($err)) {
                    Logger::error('Error#'.$err['message'].'<br>');
                    Logger::error('Line#'.$err['line'].'<br>');
                    Logger::error('File#'.$err['file'].'<br>');
                }
            });
            
        }
        
    }
?>