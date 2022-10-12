<?php

namespace Core\Event;

class Event
{
    /**
     * @var array
     */
    private static  $events = [];

    /**
     * @param $name
     * @param $callback
     */
    public static function listen($name, $callback)
    {
        self::$events[$name][] = $callback;
    }

    /**
     * @param $name
     * @param null $argument
     */
    public static function trigger($name, $argument = null)
    {
        foreach (self::$events[$name] as $_event => $callback) {
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
