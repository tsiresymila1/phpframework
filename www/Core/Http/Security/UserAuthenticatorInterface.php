<?php

namespace Core\Http\Security;

interface UserAuthenticatorInterface
{
    public function  onAuthenticateSuccess($data);
    public function  onAuthenticateFail();
    public function  onApiAuthenticateSuccess($data);
    public function  onApiAuthenticateFail();
}
