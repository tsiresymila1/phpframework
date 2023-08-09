<?php

use Core\Http\Response;
use Core\Http\Security\Auth;

function startsWith($haystack, $needle)
{
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function env($key, $default = null)
{
    return getenv($key) ?? $default;
}

function view($template, $context = [])
{
    return Response::Render($template, $context);
}

function json($data = [], $status = 200)
{
    return Response::Json($data, $status);
}

function download($filename, $headers = [])
{
    return Response::Download($filename, $headers);
}

function redirect($name)
{
    return Response::Redirect($name);
}

function user()
{
    return Auth::user();
}

function consoleError($str)
{
    echo "\033[31m\xE2\x9D\x8C $str \033[0m\n";
}

function consoleSucess($str)
{
    echo "\033[32m\xE2\x9C\x85 $str  \033[0m\n";
}

function consoleWarning($str)
{
    echo "\033[33m\xE2\x9A\xA0$str \033[0m\n";
}
function consoleInfo($str)
{
    echo "\033[36m\xE2\x84\xB9$str \033[0m\n";
}