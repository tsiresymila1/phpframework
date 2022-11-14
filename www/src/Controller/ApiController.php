<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Request;
use Core\Utils\Logger;

class ApiController extends Controller
{

    public function index()
    {
        $file = Request::File('file');
        if($file){
            $filepath = $file->upload(null,true);
            Logger::success($filepath);
            return downloadFile($filepath);
        }
        
        return json(["files"=> "ApiController"]);    
    }
}