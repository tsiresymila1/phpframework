<?php

namespace App\Controller;

use Core\Database\DB;
use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->post();
        $userm = DB::table('users')->get()->rows();
        return Response::Json(array_merge($userm,$data,['error'=>null, 'auth' => true]));
    }
}
