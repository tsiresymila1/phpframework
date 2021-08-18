<?php
    namespace Core\Http\Security;
    interface UserAuthenticatorInterface {
        public function  onAuthenticateSuccess();
        public function  onAuthenticateFail();
    }

?>