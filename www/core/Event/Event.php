<?php

namespace Core\Event;

class Event
{
    /**
     * @var array
     */
    private  $events = [];

    private static $_instance = null;

    public function get($key)
    {
        return $this->events[$key];
    }

    public function add($key, $value)
    {
        $this->events[$key] = $value;
    }

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new Event();
        }
        return self::$_instance;
    }

    /**
     * @param $name
     * @param $callback
     */
    public static function listen($name, $callback)
    {
        $ins = self::instance();
        $ins->add($name, $callback);
    }

    /**
     * @param $name
     * @param null $argument
     */
    public static function trigger($name, $argument = null)
    {
        $ins = self::instance();
        foreach ($ins->get($name) as $_event => $callback) {
            if ($argument && is_array($argument)) {
                call_user_func_array($callback, $argument);
            } elseif ($argument && !is_array($argument)) {
                call_user_func($callback, $argument);
            } else {
                call_user_func($callback);
            }
        }
    }
}
