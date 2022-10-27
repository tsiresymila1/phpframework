<?php

namespace Core\Service;

use Core\Http\Request;

class Mailer
{
    public function __construct(Request $request)
    {
        $inputs = $request->input();
    }
}
