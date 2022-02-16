<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Response;

class TestController extends Controller
{

    public function index()
    {
        Response::render("test.index", []);
    }
}
        