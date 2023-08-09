<?php

namespace Core\Http\Security;
use Core\Http\Security\Policy;

class Permission
{
    public $name;

    private $roles = [];

    private static $_instance = null;

    private static function instance()
    {
        return self::$_instance != null ? self::$_instance : new Permission();
    }

    public function roles(){
        return array_reduce($this->roles,function($data,$r){
            $data[] = $r->name;
            return $data;
        },[]);
    }

    public static function create($name)
    {
        $ins = self::instance();
        $ins->name = $name;
        Policy::addPermission($ins);
        return $ins;
    }
    public function assignRole(Role &$role)
    {
        $this->roles[] = $role;
    }

    public function syncRoles(Role &...$roles)
    {
        $this->roles = array_merge($this->roles, $roles);
    }


    public function revokeRoleTo(Role &$role)
    {
        $this->roles = array_filter($this->roles, function ($el) use ($role) {
            return $el != $role;
        });
    }
}
