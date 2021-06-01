<?php
        $routes = [
            "/" => [
                "method" =>"GET",
                "controller" => "DefaultController@index",
                "middlewares" => [
                    "SecurityMiddleware",
                ]
            ],
            "/admin" => [
                "method" =>"GET",
                "controller" => "DefaultController@admin",
                
            ],
            "/json" => [
                "method" =>"GET",
                "controller" => "DefaultController@json",
                
            ]
        ];

?>

