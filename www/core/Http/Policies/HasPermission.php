<?php

namespace Core\Http\Policies;

use Core\Http\Security\Permission;
use Core\Http\Security\Policy;

trait HasPermission
{
    private $permissions = [];

    private function loadllPermission()
    {
        foreach ($this->roles ?? [] as $role) {
            $rolep = Policy::findRole($role);
            if($rolep){
                $this->permissions = array_unique(array_merge($this->permissions, $rolep->permissions()));
            }
            
        }
    }

    public function can($access)
    {

        $this->loadllPermission();
        return in_array($access, $this->permissions);
    }

    public function hasPermissionTo($access)
    {
        $this->loadllPermission();
        return in_array($access, $this->permissions);
    }

    public function hasAnyPermission(...$permissions)
    {
        $this->loadllPermission();
        $intersec = array_intersect($this->permissions, $permissions);
        return sizeof($intersec) >= 0;
    }

    public function hasAllPermissions(...$permissions)
    {
        $this->loadllPermission();
        $intersec = array_intersect($this->permissions, $permissions);
        return sizeof($intersec) <= 0;
    }

    public function givePermissionTo($access)
    {
        if ($access instanceof Permission) {
            $this->permissions[] = $access->name;
        } else {
            $this->permissions[] = $access;
        }
        $this->permissions = array_unique($this->permissions);
    }

    public function giveAnyPermission(...$permissions)
    {
        foreach ($permissions as $access) {
            if ($access instanceof Permission) {
                $this->permissions[] = $access->name;
            } else {
                $this->permissions[] = $access;
            }
        }
        $this->permissions = array_unique($this->permissions);
    }
}
