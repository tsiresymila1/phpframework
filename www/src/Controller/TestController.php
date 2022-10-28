<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;

class TestController extends Controller
{

    public function index()
    {
        return view("test.index", ["name"=> "TestController"]); 
    }
}
        