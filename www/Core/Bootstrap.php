<?php

namespace Core;

use Core\Command\CommandContainer;
use Core\Database\DBAdapter;
use Core\Http\Exception\ErrorRender;
use Core\Http\Handler;
use Core\Http\Request;
use Core\Http\Response;
use Core\Session\Session;
use Core\Utils\Logger;
use Core\Utils\Dotenv;

class Bootstrap
{
    public static function boot()
    {
        define('__START_TIME', microtime(true));
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_PARSE & ~E_DEPRECATED);
        static::handleError();
        (new DotEnv(DIR . '/.env'))->load();
        Session::Init();
        Handler::Init();
        DBAdapter::Init();
        Handler::handle();
    }

    public static function load()
    {
        (new DotEnv(DIR . '/.env'))->load();
        CommandContainer::Init();
        DBAdapter::Init();
    }

    public static function handleError()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
            Logger::error($errno);
            Logger::error($errstr);
            Logger::error($errfile);
            Logger::error($errline);
            if (!defined('DEBUG') || DEBUG == true) {
                $strace = debug_backtrace();
                $withCode = array_key_exists(strval($errno), ErrorRender::$code);
                if (Request::isAPI()) {
                    header('Content-type:application/json;charset=utf-8');
                    echo json_encode(array(
                        "code" => $errno,
                        "error" => $errstr,
                        "file" => $errfile,
                        "line" => $errline
                    ));
                } else {
                    ob_end_clean();
                    echo ErrorRender::showErrorDetails($errstr . ' in ' . $errfile . ' on line ' . $errline, $strace, $withCode ? $errno : '500');
                }
            }
            exit();
        }, E_ALL | E_STRICT | E_ERROR | E_WARNING | E_NOTICE | E_DEPRECATED | E_USER_ERROR | E_USER_WARNING);

        set_exception_handler(function ($e) {
            $errors = array(
                E_USER_ERROR        => "User Error",
                E_USER_WARNING      => "User Warning",
                E_USER_NOTICE       => "User Notice",
            );
            $message = $e->getMessage();
            Logger::error($message . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . '==>' . $e->getCode());
            Logger::error($e->getTraceAsString());

            // enable cors :
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Max-Age: 856000');
            }
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
                }
                header("HTTP/1.1 200 OK");
                exit(0);
            }
            if (!defined('DEBUG') || DEBUG == true) {
                $withCode = array_key_exists(strval($e->getCode()), ErrorRender::$code);
                if (Request::isAPI()) {
                    header('Content-type:application/json;charset=utf-8');
                    ob_start();
                    echo json_encode(array(
                        "code" => $e->getCode(),
                        "error" => $message,
                        "file" => $e->getFile(),
                        "line" => $e->getLine(),
                    ));
                    ob_flush();
                } else {
                    ob_end_clean();
                    ob_start();
                    echo ErrorRender::showErrorDetails($message . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), $e->getTrace(), $withCode ? $e->getCode() : '500');
                    ob_flush();
                }
            };
            exit(200);
        });

        register_shutdown_function(function () {

            $err = error_get_last();
            if (!is_null($err)) {
                Logger::error('Error#' . $err['message'] . '<br>');
                Logger::error('Line#' . $err['line'] . '<br>');
                Logger::error('File#' . $err['file'] . '<br>');
                if (defined('DEBUG') && DEBUG == false) {
                    ob_end_clean();
                    echo ErrorRender::showError();
                }
            }
        });
    }
}
