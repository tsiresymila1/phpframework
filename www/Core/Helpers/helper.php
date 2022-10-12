<?php

use Core\Http\Response;

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function env($key){
    return getenv($key);
}

function view($template, $context = []){
    return Response::Render($template, $context);
}

function json($data = [], $status = 200){
    return Response::Json($data,$status);
}
