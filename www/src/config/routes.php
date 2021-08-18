<?php

       use Core\Http\Router;

       Router::all("/admin","DefaultController@admin");
       Router::get("/","DefaultController@index");
      //Router::get("/admin","DefaultController@admin",["AuthMiddleware"]);
       Router::all("/login","LoginController@login");
       Router::get("/teste","DefaultController@webpack");

?>