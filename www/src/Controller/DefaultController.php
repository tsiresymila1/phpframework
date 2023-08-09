<?php

namespace App\Controller;


use Core\Http\CoreControllers\Controller;
use Core\Utils\Encryption;
use Core\Http\Response;
use Core\Http\Security\Auth;
use Core\Session\Session;

class DefaultController extends Controller
{

    public function __construct()
    {

        Response::$renderer->addFunction('lower', function ($data) {
            return strtolower($data);
        });
        parent::__construct();
    }

    public function index(Session $session, Encryption $encrypt)
    {
        // $user = new User();
        // // get all 
        // DB::table('users')->get()->first();
        // //insert 
        // $user->name = "tsiresy";
        // $user->email = "tsiresymila@gmail.com";
        // $user->password = $encrypt->encode("Tsiresy_wp1");
        // $user->save();
        // $user->name = "mila";
        // // update 
        // User::update(['name'=>"Update"])->where('id', 1)->save();
        // //delete
        // User::delete()->whereNull('name')->save();
        // Logger::error("Error");
        // Logger::success($user);
        // Logger::warning([1,2,3,4,5]);

        return view('home.index', []);
    }

    public function admin()
    {
        $user = Auth::user();
        $is_admin = $user->hasRole('admin');
        $acces_to_post = $user->can('post');
        $is_superadmin = $user->hasRole('super-admin');
        return Response::Json(['user' => $user->toArray(), "Is Admin" => $is_admin, "Can access post" => $acces_to_post, "Is Super Admin" => $is_superadmin]);
    }

    public function json()
    {
        return Response::Json(['key' => "valuen"]);
    }
}
