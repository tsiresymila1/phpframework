<?php

namespace App\Security;

use Core\Http\Security\UserAuthenticator;

class LoginAuthenticator extends UserAuthenticator
{

    public $login = '/login';
    public $logout = "/logout";

    public function onAuthenticateFail()
    {
        return redirect("app_login");
    }
    public function onAuthenticateSuccess($user)
    {
        return redirect('admin');
    }
    public function  onApiAuthenticateFail()
    {
        return json(['error' => 'Not authenticated', 'auth'=>false]);
    }
    public function  onApiAuthenticateSuccess($user,$token)
    {
        return json(array_merge(['user'=>$user->toArray()],['error'=>null, 'auth' => true, 'token' => $token])); 
    }
}
