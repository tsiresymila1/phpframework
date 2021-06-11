<?php
        $routes = [
            "/" => [
                "method" =>"GET",
                "controller" => "DefaultController@index",
                
            ],
            "/" => [
                "method" =>"GET",
                "controller" => "DefaultController@index",
                
            ],
            "/admin" => [
                "method" =>"GET|POST",
                "controller" => "DefaultController@admin",
                "middlewares" => [
                    "AuthMiddleware",
                ]
                
            ],
            "/admin/:userid/" => [
                "method" =>"GET|POST",
                "controller" => "DefaultController@admin",
                
            ],
            
            "/login" => [
                "method" =>"GET|POST",
                "controller" => "LoginController@login",
                
            ],
            
        ];

?>

