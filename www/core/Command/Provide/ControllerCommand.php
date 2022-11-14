<?php

namespace Core\Command\Provide;

use Core\Command\Command;

class ControllerCommand extends Command
{
    public $name = "create:controller";
    public $path = APP_PATH . 'Controller' . DIRECTORY_SEPARATOR;
    public $template = APP_PATH . 'templates' . DIRECTORY_SEPARATOR;
    public  $routepath = APP_PATH . 'config' . DIRECTORY_SEPARATOR . 'routes.php';

    public $description = "Generate controller ";

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
            if (!file_exists($filename)) {

                //put controller
                $apikey = array_search('--api', $args);
                if ($apikey) {
                    $content = $this->getContent($prefix, true);
                    file_put_contents($this->routepath,  'Route::Get("/' . $prefix . '", "' . $ctrname . '@index")->name("' . $prefix . '")->asApi();' . PHP_EOL, FILE_APPEND | LOCK_EX);
                } else {
                    $content = $this->getContent($prefix);
                    //put template
                    $key = array_search('--jsbundle', $args);
                    if ($key && sizeof($args) > $key + 1) {
                        $templateContent = $this->templateContent($args[$key + 1]);
                        // file_put_contents($this->routepath,  'Route::Get("*", "' . $ctrname . '@index")->name("' . $prefix . '");' . PHP_EOL, FILE_APPEND | LOCK_EX);
                        file_put_contents($this->routepath,  'Route::Get("/' . $prefix . '/*", "' . $ctrname . '@index")->name("' . $prefix . '_route");' . PHP_EOL, FILE_APPEND | LOCK_EX);
                    } else {
                        $templateContent = $this->templateContent();
                        file_put_contents($this->routepath,  'Route::Get("/' . $prefix . '", "' . $ctrname . '@index")->name("' . $prefix . '");' . PHP_EOL, FILE_APPEND | LOCK_EX);
                    }
                    $templatepath = $this->template . $prefix;
                    if (!file_exists($templatepath)) {
                        mkdir($templatepath, 0777, true);
                    }
                    file_put_contents($templatepath . DIRECTORY_SEPARATOR . 'index.php', $templateContent);
                }
                file_put_contents($filename, $content);

                echo $ctrname . ' was created successfully' . "\n";
            } else {
                echo $ctrname . ' alread exist';
            }
        } else {
            echo 'Controller name not provided';
        }
    }

    public function getContent($name, $isAPi = false)
    {
        $cap = ucfirst($name);
        $render = $isAPi ? <<<PHP
        json(["name"=> "{$cap}Controller"])
        PHP :  <<<PHP
        view("{$name}.index", ["name"=> "{$cap}Controller"])
        PHP;

        return <<<EOL
        <?php
        namespace App\Controller;

        use Core\Http\CoreControllers\Controller;

        class {$cap}Controller extends Controller
        {

            public function index()
            {
                return {$render};
            }
        }
        EOL;
    }

    public function templateContent($jsbundle = null)
    {
        $jspath = $jsbundle ? "{{ vite('{$jsbundle}') }}" : '';
        $div = $jsbundle ? '<div id="root"></div>' : '<div>Hello from {{ name }}</div>';
        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>
            {{ name }}
            </title>
            $jspath 
        </head>

        <body>
            $div
        </body>

        </html>
        HTML;
    }
}
