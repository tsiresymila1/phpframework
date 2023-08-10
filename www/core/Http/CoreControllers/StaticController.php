<?php

namespace Core\Http\CoreControllers;

use Core\Http\Request;
use Core\Http\Response;
use Core\Storage\Storage;
use Core\Utils\Encryption;

class StaticController extends Controller
{

    public function public ($path)
    {
        $fullpath = Storage::path($path);
        if (file_exists($fullpath)) {
            return download($fullpath, false);
        } else {
            setHeader('Content-type', 'text/html');
            return Response::send("File {$path} not found in public storage!", 404);
        }

    }

    public function private ($path, Encryption $encryption, Request $request)
    {
        $key = $request->input('key', '');
        $decoded = $encryption->decode($key);
        if(!$decoded || $decoded != $path){
            setHeader('Content-type', 'text/html');
            return Response::Send("Dont have any permission for file {$path} in private storage! ", 403, false);
        }
        $fullpath = Storage::pathPrivate($path);
        if (file_exists($fullpath)) {
            return download($fullpath, false);
        } else {
            setHeader('Content-type', 'text/html');
            return Response::Send("File {$path} not found in private storage! ", 404,false);
        }
    }

}