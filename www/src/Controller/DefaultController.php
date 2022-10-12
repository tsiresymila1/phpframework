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
        $user = new User();
        // get all 
        $userm = DB::table('users')->get()->first();

        //insert 
        $user->name = "tsiresy";
        $user->email = "tsiresymila@gmail.com";
        $user->password = $encrypt->encode("Tsiresy_wp1");
        $user->roles = "ROLE_ADMIN";
        $user->save();
        $user->name = "mila";
        // update 
        User::update(['name'=>"Update"])->where('id', 1)->save();
        //delete
        User::delete()->whereNull('name')->save();
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
