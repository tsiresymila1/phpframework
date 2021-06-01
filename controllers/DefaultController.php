<?php

namespace Controllers;

use Core\Http\CoreControllers\Controller;
use Models\UserModel;

class DefaultController extends Controller{

    public function __construct()
    {
        parent::__construct();
    }
    public  function index(){
        $user = new UserModel();
        $result = $user->select()::get();
        $this->response::send($user::$tablename . ' ');
    }

    public  function admin(){
        $this->response::render('admin.html.twig',['name' => 'Tsiresy Milà','occupation' => 'Developper']);
    }

    public  function json(){
        $this->response::json(['key'=> "valuen"]);
    }
}

?>