<?php

namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Utils\Encryption;
use App\Model\UserModel;
use Core\Database\DB;
use Core\Http\Request;
use Core\Http\Response;

class DefaultController extends Controller
{

    public function __construct()
    {

        Response::$renderer->addFunction('lower', function ($data) {
            return strtolower($data);
        });
        parent::__construct();
    }

    public  function index(UserModel $user)
    {
        $result = $user->findAll()->orWhere(array('email' => "tsiresymila@gmail.com", 'soft_deleted' => 0))->where(array('id' => 1))->get();
        $encrypt = new Encryption();
        $userm = $user->findOneBy(["email" => "tsiresy@gmail.com"]);
        if (!$userm) {
            $user->insert(array(
                "name" => "Tsiresy",
                "email" => "tsiresy@gmail.com",
                "password" => $encrypt->encode("Tsiresy_wp1"),
                "roles" => "ROLE_ADMIN"
            ));
        }
        $user->set(array('password' => $encrypt->encode("Tsiresy_wp1")))->where(['email' => 'tsiresymila@gmail.com'])->update();
        return Response::Json($result);
    }

    public  function admin()
    {
        return Response::Json(['data' => "okey"]);
        // Response::Render('admin',['name' => 'Tsiresy Milà','occupation' => 'Developper']);
    }
    public  function webpack()
    {
        return Response::Render('test.html.twig', ['name' => 'Tsiresy Milà', 'occupation' => 'Developper']);
    }

    public  function json()
    {
        return Response::Json(['key' => "valuen"]);
    }
}
