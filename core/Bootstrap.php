<?php

    namespace Core;

    use Core\Database\DB;
    use Core\Http\Handler;
    use Core\Session\Session;

    class Bootstrap {
        public static function boot(){
            DB::init();
            Session::init();
            Handler::handle();
        }
    }

?>