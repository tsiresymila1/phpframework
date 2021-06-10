<?php

    namespace Controllers;
    use Core\Http\CoreControllers\Controller;
    use Core\Http\Request;

    class LoginController extends Controller{
        public function login(){
            if(Request::isGet()){

            }
            else{

            }
            $this->response->render("login.html.twig",[]);
        }

    }
?>