<?php

namespace Core\Utils;

use DateTime;

class Logger
{
    private static  $_instance = null;
    public $logs = ['messages' => [], 'request' => [], 'response' => [],'exceptions' => [],'queries'=>[],'time' =>0];

    private static $path = DIR . "storage/logs/server.log";
    
    /**
     * instance
     *
     * @return Logger
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Logger();
        }
        return self::$_instance;
    }

    public static function addQuery($query,$params,$flag = "QUERY")
    {
        $log = array_reduce(array_keys($params),function($text, $key) use($params){
            $value = $params[$key];
            return str_replace(':'.$key,is_numeric($value) ? $value : "'$value'",$text);
        },$query);
        $ins = self::instance();
        $ins->logs['queries'][] = $log.' => ('.implode(',',$params).')';
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function addException($exception, $flag = "QUERY")
    {
        $ins = self::instance();
        $ins->logs['exceptions'][] = $exception;
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($exception) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
 
    public static function log($log, $flag = "LOG")
    {
        // $ins = self::instance();
        // $ins->logs['messages'][] = ['type' => "debug","message" => $log,"time"=>0];
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function error($log, $flag = "ERROR")
    {
        $ins = self::instance();
        $ins->logs['messages'][] = ['type' => "danger","message" => $log];
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    public static function info($log, $flag = "INFOS")
    {
        $ins = self::instance();
        $ins->logs['messages'][] = ['type' => "info","message" => $log];
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    public static function warning($log, $flag = "WARNING")
    {
        $ins = self::instance();
        $ins->logs['messages'][] = ['type' => "warning","message" => $log];
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    public static function success($log, $flag = "SUCCESS")
    {
        $ins = self::instance();
        $ins->logs['messages'][] = ['type' => "success","message" => $log];
        $date = new DateTime();
        $datestring = $date->format('Y-m-d H:i:s');
        file_put_contents(self::$path, $datestring . "::" . $flag . ":: " . json_encode($log) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
