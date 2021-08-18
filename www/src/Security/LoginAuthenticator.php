<?php

    namespace App\Security;

    use Core\Http\Response;
    use Core\Http\Security\UserAuthenticator;

    class LoginAuthenticator extends UserAuthenticator {

        public function onAuthenticateFail(){
            Response::redirect("/login");
        }
        
    }

?>