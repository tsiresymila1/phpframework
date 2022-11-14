<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Session\Session;

class TestController extends Controller
{

    public function index(Session $session)
    {
        return view("test.index", ["name"=> "TestController"]); 
    }
}
        