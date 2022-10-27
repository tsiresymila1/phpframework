<?php

namespace App\Controller;

use App\Model\User;
use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Service\Mailer;

class LoginController extends Controller
{

    public function login(Request $request, Mailer $mailer)
    {
        $data = $request->post();
        $user = User::findOne(1);
        $users = User::orWhere('name', $data['email'])->where('email', $data['email'])->andWhere('password', $data['password'])->get()->rows();
        $all = User::findAll();
        return json(array_merge($users,$data,['error'=>null, 'auth' => true])); 
    }
}
