<?php

namespace Core\Database;

use Core\Database\Eloquent\EloquentModel;
use Core\Http\Policies\HasPermission;
use Core\Http\Policies\HasRole;

abstract class UserAuthenticatorModel extends EloquentModel
{

    public $roles = [];
    use HasRole, HasPermission;

    abstract public function getRoles();
}
