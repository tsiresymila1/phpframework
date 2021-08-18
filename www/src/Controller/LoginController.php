<?php

    namespace App\Controller;
    use Core\Http\CoreControllers\Controller;
    use Core\Http\Request;
    use Core\Http\Response;

    class LoginController extends Controller{

        public function login(){

            if(Request::isAuth()){
                Response::Redirect('/admin');
            }
            else{
                
            }
            Response::render("login",[]);
        }

    }
?>