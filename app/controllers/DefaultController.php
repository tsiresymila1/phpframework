<?php

namespace Controllers;

use Core\Http\CoreControllers\Controller;
use Core\Utils\Encryption;
use Models\UserModel;

class DefaultController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }
    
    public  function index(){
        $user = new UserModel();
        $result = $user->findAll()->orWhere(array('email'=>"tsiresymila@gmail.com",'soft_deleted'=>0))->where(array('id'=>1))->orWhere(array('email'=>"tsiresymila@gmail.com",'soft_deleted'=>0))->get();
        $user->find([1,2,3]);
        $user->findBy('id',1);
        $user->findAll()->get();
        $encrypt = new Encryption(); 
        // $user->insert(array(
        //     "name"=>"Tsiresy",
        //     "email" =>"tsiresymila@gmail.com",
        //     "password" => $encrypt->encode("tsiresy")
        // ));
        $user->set(array('password'=>$encrypt->encode("mila")))->where('id',2)->update();
        $this->response::json($result);
    }

    public  function admin(){
        $this->response::render('admin.html.twig',['name' => 'Tsiresy Milà','occupation' => 'Developper']);
    }

    public  function json(){
        $this->response::json(['key'=> "valuen"]);
    }
}

?>