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
        $reflect = new ReflectionClass(new User());
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($props as $prop){
            $doc = $prop->getDocComment();
            $bloc = new DocBlock($doc);
        }
        $user = User::findOne(1);
        $files = $user->files;
        $users = User::orWhere('name', $data['username'])->where('email', $data['username'])->andWhere('password', $data['password'])->get()->rows();
        return Response::Json(array_merge($users,$data,['error'=>null, 'auth' => true]));
    }
}
