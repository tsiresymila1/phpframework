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
        $required = str_replace("\\", DIRECTORY_SEPARATOR, $filepath);
        require $required;
    }
}
define('DIR', dirname(dirname(__FILE__)) . '/');
define('APP_PATH', dirname(dirname(__FILE__)) . '/src' . '/');
if (!file_exists(APP_PATH . 'config/config.php')) {
    throw new Exception('config.php file not found');
}
require APP_PATH . 'config/config.php';
if (file_exists(APP_PATH . 'config/autoload.php')) {
    require APP_PATH . 'config/autoload.php';
}
Autoloader::register();
