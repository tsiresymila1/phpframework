<?php
return [
    'secret' => '7c32d31dbdd39f2111da0b1dea59e94f3ed715fd8cdf0ca3ecf354ca1a2e3e30',
    "authenticator" => "App\Security\LoginAuthenticator",
    "url" => ["/api"],
    "excludes" => ["/api/docs"],
    "model" => "App\Model\UserModel",
    "config" => [
        "username" => "username",
        "password" => "password",
        "roles" => "ROLE_ADMIN"
    ]
];
