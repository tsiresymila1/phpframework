<?php

namespace Core\Http\CoreMiddlewares;

use Core\Http\Request;

class JwtCoreMiddleware implements Middleware
{

    public function handle()
    {
        $header = Request::headers();
    }
}
