<?php

namespace Core\Debugbar;

use Core\Utils\Logger;
use Core\Http\Request;
use Core\Http\Response;

class Debugbar
{
    public static function show()
    {
        $ins = self::load();
        $logs = json_encode($ins->logs, JSON_HEX_QUOT);
        $file = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'index';
        $js = file_get_contents($file . '.js');
        $css = file_get_contents($file . '.css');

        echo '<div id="debugbar"></div>';
        echo '<script type="text/javascript">
            var debugbarJSON = JSON.stringify(' . $logs . ');
            document.getElementById("debugbar").setAttribute("data",debugbarJSON);
            </script>';
        echo '<script type="module" crossorigin>' . $js . '</script>';
        echo '<style>' . $css . '</style>';
    }

    public static function setResponse($status, $data,$type){
        $ins = Logger::instance();
        $ins->logs['response'] = ['status'=>$status,'data'=>$data, 'type' => $type];
    }
    
    /**
     * load
     *
     * @return Logger
     */
    public static function load()
    {
        $time = (microtime(true) - __START_TIME) * 1000;
        $ins = Logger::instance();
        $reqinfo = (array)Request::instance();
        $req = array_reduce(array_keys($reqinfo), function ($prev, $key) use ($reqinfo) {
            $prev[trim(str_replace(['*', ' '], '', $key))] = $reqinfo[$key];
            return $prev;
        }, []);
        unset($req['auth']);
        $ins->logs['request'] = $req;
        $ins->logs['time'] = $time;
        $ins->logs['method'] = $req['method'];
        $ins->logs['path'] = $req['path'];
        unset($req['method']);
        unset($req['path']);
        return $ins;
    }
}
