<?php

    namespace App\Security;

    use Core\Http\Response;
    use Core\Http\Security\UserAuthenticator;

    class LoginAuthenticator extends UserAuthenticator {

        public function onAuthenticateFail(){
            Response::Redirect("app_login");
        }
        public function onAuthenticateSuccess($user){
            Response::Redirect('admin');
        }
        
        
    }

?>