<?php

namespace Core\Http\CoreControllers;

use Core\Http\Exception\ErrorRender;
use Core\Http\Request;
use Core\Http\Response;

class Controller
{
    protected $request;

    protected $response;

    public function __construct()
    {
        $this->request = Request::instance();
        $this->response = Response::instance();
    }

    public function __invoke(...$args)
    {
        return Response::send(static::class . " was called ");
    }
    public function url404NotFound()
    {
        setHeader('Content-type','text/html');
        return Response::send(ErrorRender::showError(404, 'Page not found', true));
    }

}
