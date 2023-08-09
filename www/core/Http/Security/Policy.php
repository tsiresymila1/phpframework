<?php

namespace Core\Http\Security;
use Core\Http\Security\Role;
use Core\Http\Security\Permission;

class Policy
{
    private static $_instance = null;
    private  $roles = [];
    private $permissions = [];

    public function role($role){
        $this->roles[] = $role;
    }
    public function permisison($access){
        $this->permissions[] = $access;
    }

    /**
     * instance
     *
     * @return Policy
     */
    public static function instance()
    {
        return self::$_instance = self::$_instance = self::$_instance != null ? self::$_instance : new Policy();
    }

    public static function permissions()
    {
        return self::instance()->permissions;
    }
    public static function roles()
    {
        return self::instance()->roles;
    }

    public static function addRole(Role &$role){
        $ins = self::instance();
        $ins->role($role);
    }
    public static function addPermission(Permission &$permission){
        $ins = self::instance();
        $ins->permisison($permission);
    }
    
    /**
     * findRole
     *
     * @param  string $role
     * @return Role | null
     */
    public static function findRole($role){
        $ins = self::instance();
        $rep = array_filter($ins->roles,function($r) use($role){
            return $role == $r->name;
        });
        if(sizeof($rep) > 0){
            return array_values($rep)[0];
        }else{
            return null;
        }
    }    
    /**
     * findPermission
     *
     * @param  string $access
     * @return Permission | null
     */
    public static function findPermission($access){
        $ins = self::instance();
        $rep =  array_filter($ins->permissions,function($p) use($access){
            return $access == $p->name;
        });
        if(sizeof($rep) > 0){
            return array_values($rep)[0];
        }else{
            return null;
        }
    }
}
