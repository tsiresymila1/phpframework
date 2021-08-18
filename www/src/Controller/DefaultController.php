<?php

namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Utils\Encryption;
use App\Model\UserModel;
use Core\Http\Response;

class DefaultController extends Controller {

    public function __construct()
    {
        parent::__construct();
    }
    
    public  function index(){
        $user = new UserModel();
        $result = $user->findAll()->orWhere(array('email'=>"tsiresymila@gmail.com",'soft_deleted'=>0))->where(array('id'=>1))->get();
        $encrypt = new Encryption(); 
        $userm = $user->findOneBy(["email" =>"tsiresy@gmail.com"]);
        if(!$userm){
            $user->insert(array(
                "name"=>"Tsiresy",
                "email" =>"tsiresy@gmail.com",
                "password" => $encrypt->encode("Tsiresy_wp1"),
                "roles" => "ROLE_ADMIN"
            ));
        }
        $user->set(array('password'=>$encrypt->encode("Tsiresy_wp1")))->where(['email'=>'tsiresymila@gmail.com'])->update();
        Response::Json($result);
    }

    public  function admin(Response $response){
        $response::render('admin',['name' => 'Tsiresy Milà','occupation' => 'Developper']);
    }
    public  function webpack(Response $response){
        $response::Render('test.html.twig',['name' => 'Tsiresy Milà','occupation' => 'Developper']);
    }

    public  function json(Response $response){
        $response::Json(['key'=> "valuen"]);
    }

}

?>