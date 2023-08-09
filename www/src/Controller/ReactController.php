<?php
namespace App\Controller;
use App\Model\User;
use Core\Http\CoreControllers\Controller;
use Core\Utils\Logger;
class ReactController extends Controller
{

    public function index()
    {
        Logger::error("Error");
        Logger::success("test print");
        Logger::warning([1,2,"Hello from React controller string ",4,5]);
        $user = User::first();
        return view("react.index", ["name"=> "ReactController", "users"=> $user]);
    }
}
        