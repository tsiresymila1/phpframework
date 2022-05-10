<?php

namespace App\Controller;

use Core\Database\DB;
use Core\Http\CoreControllers\Controller;
use Core\Utils\Encryption;
use App\Model\User;
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

    public  function index()
    {
        $encrypt = new Encryption();
        $userm = DB::table('users')->get();
        if (!$userm) {
            $user->name = "tsiresy";
            $user->email = "tsiresymila@gmail.com";
            $user->password = $encrypt->encode("Tsiresy_wp1");
            $user->roles = "ROLE_ADMIN";
            $user->save();
        }
        else{
            $userm->delete();
        }
        //$user->set(array('password' => $encrypt->encode("Tsiresy_wp1")))->where(['email' => 'tsiresymila@gmail.com'])->update();
        return Response::Json($userm);
    }

    public  function admin()
    {
        return Response::Json(['data' => "okey"]);
    }

    public  function json()
    {
        return Response::Json(['key' => "valuen"]);
    }
}
