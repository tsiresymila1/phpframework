<?php

namespace App;

use Core\Http\Security\Auth;
use Core\Http\Security\Permission;
use Core\Http\Security\Role;
use Core\OpenAPI\OAISchema;
use Core\OpenAPI\OpenApi;
use Core\Renderer\Template2;

class Boot
{
    public static function start()
    {

        // SETTING OPENAPI  SCHEMA

        $schemas = [
            new OAISchema("FileUploadDto", "object", [
                "file" => ["type" => "string", "format" => "binary"],
            ], ["file"]),
            new OAISchema("AuthDto", "object", [
                "username" => ["type" => "string"],
                "password" => ["type" => "string"]
            ], ["username", "password"]),
            new OAISchema("RegisterDto", "object", [
                "email" => ["type" => "string"],
                "username" => ["type" => "string"],
                "userimage" => ["type" => "string", "format" => "binary"],
                "password" => [
                    "type" => "string",
                    // "default" => "test",
                    // "exemple" => "test",
                    // "enum"=> [
                    //     "enum1",
                    //     "enum2"
                    // ]
                ],
            ], ["username", "password", "userimage", "password"]),
        ];

        // REGISTER SCHEMA 
        OpenApi::addSchema($schemas);
        // END SETUP OPENAPI SCHEMA

        Template2::addFunction('uppercase', function ($data) {
            return strtoupper($data);
        });

        Template2::addFunction('lower', function ($data) {
            return strtolower($data);
        });

        $admin = Role::create('admin');
        $create_post = Permission::create('post');

        $admin->givePermissionTo($create_post);

        $create_user = Permission::create('user');
        $superadmin = Role::create('super-admin');
        $create_user->assignRole($superadmin);


        $user = Auth::user();
        if ($user) {
            $user->assignRole($admin);
            $user->givePermissionTo($create_user);
        }
    }
}