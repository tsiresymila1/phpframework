<?php

namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class HomeController extends Controller
{

    public function index()
    {
        return Response::render("index", []);
    }
}
