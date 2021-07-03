<?php

    namespace Core;

    use Core\Database\DB;
    use Core\Http\Handler;
    use Core\Session\Session;
    use ErrorException;

    class Bootstrap {
        public static function boot(){
            set_error_handler(function($errno, $errstr, $errfile, $errline ){
                echo "<b>Error: </b> $errstr<br>";
                echo "Ending Script";
                exit();
            },E_ALL | E_STRICT | E_ERROR | E_WARNING);
            DB::init();
            Session::init();
            Handler::handle();
        }
    }

?>