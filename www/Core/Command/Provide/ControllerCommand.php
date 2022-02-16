<?php

namespace Core\Command\Provide;

use Core\Command\Command;

class ControllerCommand extends Command
{
    public $name = "provide:controller";
    public $path = APP_PATH . 'Controller' . DIRECTORY_SEPARATOR;
    public $template = APP_PATH . 'templates' . DIRECTORY_SEPARATOR;
    public  $routepath = APP_PATH . 'config' . DIRECTORY_SEPARATOR . 'routes.php';
    public function handle($args)
    {
        if (sizeof($args) > 0) {
            $ctrname = $args[0];
            $sub = substr(strtolower($ctrname), -10, 10);
            if ($sub === 'controller') {
                $prefix = strtolower(substr_replace($ctrname, '', -10, 10));
            } else {
                $prefix = strtolower($ctrname);
            }
            $ctrname = ucfirst($prefix) . 'Controller';
            $filename = $this->path . $ctrname . '.php';
            $templatepath = $this->template . $prefix;
            if (!file_exists($filename)) {
                $content = $this->getContent($prefix);
                $templateContent = $this->templateContent($ctrname);
                file_put_contents($filename, $content);
                if (!file_exists($templatepath)) {
                    mkdir($templatepath, 0777, true);
                }
                file_put_contents($templatepath . DIRECTORY_SEPARATOR . 'index.php', $templateContent);
                //register route 
                file_put_contents($this->routepath,  'Route::Get("/' . $prefix . '", "' . $ctrname . '@index")->name("' . $prefix . '");' . PHP_EOL, FILE_APPEND | LOCK_EX);
                echo $ctrname . ' was created successfully';
            } else {
                echo $ctrname . ' alread exist';
            }
        } else {
            echo 'Controller name not provide';
        }
    }

    public function getContent($name)
    {
        return '<?php
namespace App\Controller;

use Core\Http\CoreControllers\Controller;
use Core\Http\Response;

class ' . ucfirst($name) . 'Controller extends Controller
{

    public function index()
    {
        Response::render("' . $name . '.index", []);
    }
}
        ';
    }

    public function templateContent($name)
    {
        return '<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <title>
    ' . $name . '
    </title>
</head>

<body>
    <div>Hello from ' . $name . '</div>
</body>

</html>';
    }
}
