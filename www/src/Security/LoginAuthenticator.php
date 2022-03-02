<?php

namespace App\Security;

use Core\Http\Response;
use Core\Http\Security\UserAuthenticator;

class LoginAuthenticator extends UserAuthenticator
{

    public function onAuthenticateFail()
    {
        return Response::Redirect("app_login");
    }
    public function onAuthenticateSuccess($user)
    {
        return Response::Redirect('admin');
    }
    public function  onApiAuthenticateFail()
    {
        return Response::Json(['error' => null, 'auth'=>true]);
    }
}
