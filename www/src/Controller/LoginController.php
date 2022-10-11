<?php

namespace App\Controller;

use App\Model\File;
use App\Model\User;
use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Core\Utils\DocBlock;
use ReflectionClass;
use ReflectionProperty;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->post();
        $user = User::findOne(1);
        $files = $user->files;
        $users = User::orWhere('name', $data['email'])->where('email', $data['email'])->andWhere('password', $data['password'])->get()->rows();
        $all = User::findAll();
        return Response::Json(array_merge($users,$data,['error'=>null, 'auth' => true]));
    }
}
