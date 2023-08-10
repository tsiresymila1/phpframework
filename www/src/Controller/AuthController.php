<?php

namespace App\Controller;

use App\Model\User;
use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Security\Auth;
use Core\Storage\Storage;
use Core\Utils\Encryption;
use Core\Utils\JWT;

class AuthController extends Controller
{

    public function login(Request $request, JWT $jwt, Encryption $encryption)
    {
        $data = $request->post();
        $password = $encryption->encode($request->input('password'));
        $user = User::where(function ($q) use ($data) {
            $q->where('name', $data['username'])->orWhere('email', $data['username']);
        })->where('password', $password)->get()->first();
        if ($user) {
            Auth::attemp($user);
            return json(array_merge(['user' => $user->toArray()], ['error' => null, 'auth' => true, 'token' => $jwt->generate($user->id, $user->getRoles())]));
        } else {
            return json(['error' => "Not Authentificated", 'auth' => false]);
        }

    }

    public function register(Request $request, JWT $jwt, Encryption $encryption)
    {
        $data = $request->post();
        $file = Request::File('userimage');
        if ($file) {
            Storage::putPrivate($file, true);
            $data['userimage'] = $file->getSecureName();
        }
        $password = $encryption->encode($request->input('password'));
        $data['password'] = $password;
        $user = User::create($data);
        if ($user) {
            Auth::attemp($user);
            return json(array_merge([
                'url' => Storage::urlPrivate($data['userimage']),
                'user' => $user->toArray()
            ], [
                'error' => null,
                'auth' => true,
                'token' => $jwt->generate($user->id, $user->getRoles())
            ]));
        } else {
            return json(['error' => "Not Authentificated", 'auth' => false]);
        }

    }
}