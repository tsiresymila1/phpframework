<?php

namespace App\Model;

use Core\Database\UserAuthenticatorModel;

class User extends UserAuthenticatorModel
{

    protected $table = "users";

    public $fillable = ["email", "name", "password", "userimage"];

    public function getRoles()
    {
        return implode(',', $this->roles ?? []);
    }
}