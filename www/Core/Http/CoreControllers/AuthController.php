<?php

namespace Core\Http\CoreControllers;

use Core\Database\Model;
use Core\Session\Session;

class AuthController extends Controller
{

    public  $model;
    public  $usernamekey = "username";
    public  $passwordkey = "password";
    public  $sessionkey = "user";

    protected function auth()
    {
        $user = Session::get($this->sessionkey);
        if (!is_null($user)) {
        }
    }
}
