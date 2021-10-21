<?php

namespace Core\Session;

class Session
{

    private static $_instance = null;

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Session();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $sessionPath = DIR . '/storage/session/';
        if (!file_exists($sessionPath)) {
            @mkdir($sessionPath);
        }
        ini_set('session.save_path', realpath($sessionPath));
        session_start();
    }

    public static function  Set($key, $value)
    {
        if (isset($_SESSION[$key])) unset($_SESSION[$key]);
        $_SESSION[$key] = $value;
    }
    /**
     * Get
     *
     * @param mixed $key
     * @param $default = null
     *
     * @return void
     */
    public static function  Get($key, $default = null)
    {
        return isset($_SESSION[$key]) ?  $_SESSION[$key] : $default;
    }
    public static function Reset()
    {
        session_destroy();
    }
    public static function Remove($key = null)
    {
        if ($key != null && isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function Init()
    {
        return self::getInstance();
    }
}