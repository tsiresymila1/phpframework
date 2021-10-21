<?php
class Autoloader
{

    static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    static function autoload($class)
    {

        $file = $class . '.php';
        $psr = array(
            "App\\" => "src/"
        );
        $filepath = str_replace("\\", DIRECTORY_SEPARATOR, DIR . $file);
        $filepath = file_exists($filepath) ? $filepath : str_replace("\\", DIRECTORY_SEPARATOR, DIR . "libs\\" . $file);
        if (!file_exists($filepath)) {
            foreach ($psr as $key => $value) {
                $filepath = str_replace($key, $value, DIR . $file);
                $filepath = str_replace("\\", DIRECTORY_SEPARATOR, $filepath);
                if (file_exists($filepath)) {
                    break;
                }
            }
        }
        require str_replace("\\", DIRECTORY_SEPARATOR, $filepath);
    }
}
Autoloader::register();
