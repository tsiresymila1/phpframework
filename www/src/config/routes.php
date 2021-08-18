<?php

       use Core\Http\Router;
       Router::All("/admin/hello","DefaultController@admin");
       Router::Get("/","DefaultController@index");
       Router::Get("/admin","DefaultController@admin",["AuthMiddleware"]);
       Router::All("/login","LoginController@login");
       Router::Get("/teste","DefaultController@webpack");

?>