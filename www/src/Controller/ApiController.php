<?php

namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Security\Auth;
use Core\Utils\Logger;

class ApiController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        Logger::success($user);
        $file = Request::File('file');
        if ($file) {
            $filepath = $file->upload(null, true);
            Logger::success($filepath);
            return download($filepath);
        }

        return json(["files" => "ApiController"]);
    }
}
