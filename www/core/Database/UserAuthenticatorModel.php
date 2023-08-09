<?php

namespace Core\Database;

use Core\Database\Model;
use Core\Http\Policies\HasPermission;
use Core\Http\Policies\HasRole;

abstract class UserAuthenticatorModel extends Model
{

    public $roles = [];
    use HasRole, HasPermission;

    abstract public function getRoles();
}
