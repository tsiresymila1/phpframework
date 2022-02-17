<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Response;

class ReactController extends Controller
{

    public function index($react)
    {
        Response::render("react.index", ["name"=> "ReactController"]);
    }
}
        