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
            "App\\" => "src/",
            "Core\\" => "core/"
        );
        $filepath = str_replace("\\", DIRECTORY_SEPARATOR, DIR . $file);
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
        if (file_exists($required)) {
            require $required;
        }
    }
}
define('DIR', dirname(dirname(__FILE__)) . '/');
define('APP_PATH', dirname(dirname(__FILE__)) . '/src' . '/');

// load helper
include dirname(__FILE__) . '/Helpers/loader.php';

//load config
if (!file_exists(APP_PATH . 'config/config.php')) {
    throw new Exception('config.php file not found');
}
require APP_PATH . 'config/config.php';
//load autoload 
if (file_exists(APP_PATH . 'config/autoload.php')) {
    $vendor = require APP_PATH . 'config/autoload.php';
    $type = gettype($vendor);
    if ($type  == "object") {
        $vendor->register();
    }
}

Autoloader::register();
