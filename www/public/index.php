<?php

    /*
    * Author : Tsiresy Milà
    * Fullstack Developer
    * Email : tsiresymila@gmail.com
    **/
    use Core\Bootstrap;
    define('DIR', dirname(dirname( __FILE__ )) . '/' );
    define('APP_PATH', dirname(dirname( __FILE__ )) . '/src'.'/' );
    require DIR.'Core/Autoloader.php';
    // using composer, 
    // require DIR.'/vendor/autoload.php'
    Bootstrap::boot();
?>