<?php

namespace Core\Http\Security;

use Core\Http\Security\Policy;

class Role
{
    public $name;
    private  $permissions = [];
    private static $_instance = null;

    private static function instance()
    {
        return self::$_instance != null ? self::$_instance : new Role();
    }
    
    public function permissions(){
        return array_reduce($this->permissions,function($data,$p){
            $data[] = $p->name;
            return $data;
        },[]);
    }

    /**
     * create
     *
     * @param  mixed $role
     * @return Role
     */
    public static function create($role)
    {
        $ins = self::instance();
        $ins->name = $role;
        Policy::addRole($ins);
        return $ins;
    }
    public function givePermissionTo(Permission &$permission)
    {
        $this->permissions[] = $permission;
    }
    public function syncPermissions(Permission &...$permissions)
    {
        $this->permissions = array_merge($this->permissions, $permissions);
    }

    public function revokePermissionTo(Permission &$permission)
    {
        $this->permissions = array_filter($this->permissions, function ($el) use ($permission) {
            return $el != $permission;
        });
    }
}
