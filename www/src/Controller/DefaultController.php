<?php

namespace App\Controller;

use Core\Database\DB;
use Core\Http\CoreControllers\Controller;
use Core\Utils\Encryption;
use App\Model\User;
use Core\Http\Response;
use Core\Renderer\Template;
use Core\Session\Session;
use Core\Utils\Logger;

class DefaultController extends Controller
{

    public function __construct()
    {

        Response::$renderer->addFunction('lower', function ($data) {
            return strtolower($data);
        });
        parent::__construct();
    }

    public  function index(Session $session,Encryption $encrypt)
    {
        $user = new User();
        // get all 
        DB::table('users')->get()->first();
        Template::addFunction('uppercase',function($data){
            return strtoupper($data);
        });
        //insert 
        $user->name = "tsiresy";
        $user->email = "tsiresymila@gmail.com";
        $user->password = $encrypt->encode("Tsiresy_wp1");
        $user->save();
        $user->name = "mila";
        // update 
        User::update(['name'=>"Update"])->where('id', 1)->save();
        //delete
        User::delete()->whereNull('name')->save();
        Logger::error("Error");
        Logger::success($user);
        Logger::warning([1,2,3,4,5]);
        
        return view('home.index',[]);
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
