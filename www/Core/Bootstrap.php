<?php

namespace Core;

use ArgumentCountError;
use Core\Command\CommandContainer;
use Core\Database\DBAdapter;
use Core\Http\Exception\ErrorRender;
use Core\Http\Handler;
use Core\Session\Session;
use Core\Utils\Logger;

class Bootstrap
{
    public static function boot()
    {
        static::handleError();
        Session::Init();
        DBAdapter::Init();
        Handler::handle();
    }

    public static function load()
    {
        CommandContainer::Init();
        DBAdapter::Init();
    }

    public static function handleError()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline,$errcontext) {
            Logger::error($errno);
            Logger::error($errstr);
            Logger::error($errfile);
            Logger::error($errline);
            if(!defined('DEBUG') || DEBUG == true) {
                $strace = debug_backtrace();
                $withcode = array_key_exists(strval($errno),ErrorRender::$code);
                echo ErrorRender::showErrorDetails($errstr . ' in ' . $errfile . ' on line ' . $errline, $strace, $withcode ? $errno : '500');
            }
            exit();
        }, E_ALL | E_STRICT | E_ERROR | E_WARNING | E_NOTICE);

        set_exception_handler(function($e) {
            $errors = array(
                E_USER_ERROR        => "User Error",
                E_USER_WARNING      => "User Warning",
                E_USER_NOTICE       => "User Notice",
            );
            Logger::error( $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(). '==>'.$e->getCode());
            Logger::error($e->getTraceAsString());
            if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Headers: X-Requested-With');
                header("HTTP/1.1 200 OK");
                die();
            }
            if(!defined('DEBUG') || DEBUG == true) {
                $withcode = array_key_exists(strval($e->getCode()),ErrorRender::$code);
                echo ErrorRender::showErrorDetails($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), $e->getTrace(), $withcode ? $e->getCode() : '500');
            };
            exit(200);
        });

        register_shutdown_function(function () {

            $err = error_get_last();
            if (!is_null($err)) {
                Logger::error('Error#' . $err['message'] . '<br>');
                Logger::error('Line#' . $err['line'] . '<br>');
                Logger::error('File#' . $err['file'] . '<br>');
                if(defined('DEBUG') && DEBUG == false) {
                    echo ErrorRender::showError();
                }
            }
        });
    }
}
