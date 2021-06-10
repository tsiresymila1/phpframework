<?php

    namespace Core\Http\CoreControllers;

use Core\Database\Model;
use Core\Session\Session;

class AuthController extends Controller {

        public Model $model;
        public String $usernamekey = "username";
        public String $passwordkey = "password";
        public String $sessionkey = "user";

        protected function auth(){
            $user = Session::get($this->sessionkey);
            if(!is_null($user)){
                
            }
        }

    }

?>