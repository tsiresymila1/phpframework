<?php

    use Core\Bootstrap;
    define( 'DIR', dirname(dirname( __FILE__ )) . '/' );
    require DIR.'/autoloader.php'; 
    Bootstrap::boot();
?>