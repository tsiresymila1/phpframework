<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Response;

class KantoController extends Controller
{

    public function index()
    {
        Response::render("kanto.index", ["name"=> "KantoController"]);
    }
}
        