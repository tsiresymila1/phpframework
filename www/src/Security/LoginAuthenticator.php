<?php

namespace App\Security;

use Core\Http\Response;
use Core\Http\Security\UserAuthenticator;

class LoginAuthenticator extends UserAuthenticator
{

    public function onAuthenticateFail()
    {
        Response::Redirect("app_login");
    }
    public function onAuthenticateSuccess($user)
    {
        Response::Redirect('admin');
    }
    public function  onApiAuthenticateFail()
    {
        Response::Json(['message api' => 'Not authentified']);
    }
}
