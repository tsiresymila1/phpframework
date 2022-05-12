<?php

namespace App\Controller;

use App\Model\File;
use App\Model\User;
use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->post();
        $user = User::findOne(1);
        $files = $user->files;
        $users = User::orWhere('name', $data['username'])->where('email', $data['username'])->andWhere('password', $data['password'])->get()->rows();
        return Response::Json(array_merge($users,$data,['error'=>null, 'auth' => true]));
    }
}
