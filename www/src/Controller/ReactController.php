<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Response;

class ReactController extends Controller
{

    public function index()
    {
        Response::render("react.index", ["name"=> "ReactController"]);
    }
}
        