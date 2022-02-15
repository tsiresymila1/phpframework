<?php

namespace Core\Http\CoreControllers;

use Core\Http\Request;
use Core\Http\Response;

class Controller
{

    public function __construct()
    {
        $this->request = Request::instance();
        $this->response = Response::instance();
    }

    public function __invoke(...$args)
    {
        Response::send(static::class . " was called ");
    }
    public function url404NotFound()
    {
        Response::send(" URL not found");
    }

    public function addFunction($name, $callback)
    {
        Response::$renderer->addFunction($name, $callback);
    }
}
