<?php

use Core\Http\Response;

function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function env($key,$default=null){
    return getenv($key) ?? $default;
}

function view($template, $context = []){
    return Response::Render($template, $context);
}

function json($data = [], $status = 200){
    return Response::Json($data,$status);
}

function downloadFile($filename, $headers = []){
    return Response::Download($filename,$headers);
}
