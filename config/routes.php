<?php
        $routes = [
            "/" => [
                "method" =>"GET",
                "controller" => "DefaultController@index",
                
            ],
            "/admin" => [
                "method" =>"GET",
                "controller" => "DefaultController@admin",
                "middlewares" => [
                    "AuthMiddleware",
                ]
                
            ],
            "/login" => [
                "method" =>"GET",
                "controller" => "LoginController@login",
                
            ]
        ];

?>

