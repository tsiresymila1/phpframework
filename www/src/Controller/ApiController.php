<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;

class ApiController extends Controller
{

    public function index()
    {
        return json(["name"=> "ApiController"]);    
    }
}