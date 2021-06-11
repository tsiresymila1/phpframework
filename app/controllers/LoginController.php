<?php

    namespace Controllers;
    use Core\Http\CoreControllers\Controller;
    use Core\Http\Request;

    class LoginController extends Controller{

        public function login(){

            if(Request::isGet()){
                // $this->response->json(Request::post());
            }
            else{
                $data = Request::post();
            }
            $this->response->render("login.html.twig",[]);
        }

    }
?>