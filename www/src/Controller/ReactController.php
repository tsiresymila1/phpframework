<?php
namespace App\Controller;
use Core\Http\CoreControllers\Controller;
use Core\Http\Response;
use Core\Http\Router;

class ReactController extends Controller
{

    public function index($react)
    {
        $routes = Router::GetRoutes();
        return Response::render("react.index", ["name"=> "ReactController"]);
    }
}
        