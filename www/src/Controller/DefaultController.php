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
        parent::__construct();
    }

    public function index(Session $session, Encryption $encrypt)
    {
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
        return Response::Json(['key' => "value"]);
    }
}
