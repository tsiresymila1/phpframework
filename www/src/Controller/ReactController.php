<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Response;

class ReactController extends Controller
{

    public function index($react)
    {
        return Response::render("react.index", ["name"=> "ReactController"]);
    }
}
        