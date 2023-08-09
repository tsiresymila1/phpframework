<?php

namespace App;

use Core\Http\Security\Auth;
use Core\Http\Security\Permission;
use Core\Http\Security\Role;
use Core\Renderer\Template;

class Boot
{
    public static function start()
    {

        Template::addFunction('uppercase',function($data){ 
            return strtoupper($data);
        });

        $admin = Role::create('admin');
        $create_post = Permission::create('post');

        $admin->givePermissionTo($create_post);

        $create_user = Permission::create('user');
        $superadmin = Role::create('super-admin');
        $create_user->assignRole($superadmin);

        
        $user = Auth::user();
        if($user){
            $user->assignRole($admin);
            $user->givePermissionTo($create_user);
        }
    }
}
