<?php

namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        // $data = $request->post();
        return Response::Json(['error'=>null, 'auth' => true]);
    }
}
