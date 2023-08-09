<?php

namespace Core\Http\Policies;

use Core\Http\Security\Policy;
use Core\Http\Security\Role;

trait HasRole
{


    private function loadllRoles()
    {
        foreach ($this->permissions ?? [] as $access) {
            $perm = Policy::findPermission($access);
            if($perm){
                $this->roles = array_unique(array_merge($this->roles, $perm->roles()));
            }
        }
    }

    public function hasRole($role)
    {
        $this->loadllRoles();
        if ($role instanceof Role) {
            return in_array($role->name, $this->roles ?? []);
        } else {
            return in_array($role, $this->roles ?? []);
        }
    }

    public function hasAnyRole($roles)
    {
        $this->loadllRoles();
        $intersec = array_intersect($this->roles, $roles);
        return sizeof($intersec) >= 0;
    }

    public function hasAllRoles($roles)
    {
        $this->loadllRoles();
        $intersec = array_intersect($this->roles, $roles);
        return sizeof($intersec) <= 0;
    }

    public function assignRole($role)
    {
        if ($role instanceof Role) {
            $this->roles[] = $role->name;
        } else {
            $this->roles[] = $role;
        }
        $this->roles = array_unique($this->roles);
    }
}
