<?php
namespace App\Controller;
use Core\Http\CoreControllers\Controller;
use Core\Utils\Logger;
class ReactController extends Controller
{

    public function index()
    {
        Logger::error("Error");
        Logger::success("test print");
        Logger::warning([1,2,3,4,5]);
        return view("react.index", ["name"=> "ReactController"]);
    }
}
        