<?php
    return [
        "authenticator" => "App\Security\LoginAuthenticator",
        "url" => "/admin",
        "model" => "App\Model\UserModel",
        "config" => [
            "username" => "email",
            "password" => "password",
            "roles" => "ROLE_ADMIN"
        ]
    ]
?>