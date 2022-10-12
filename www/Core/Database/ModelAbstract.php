<?php

namespace Core\Database;

abstract class ModelAbstract {
    protected function snakeCase($string, $us = "_")
    {
        return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $us, $string));
    }
    protected function endc($array)
    {
        return end($array);
    }

    abstract public static function where($key,$value=null,$op=null);
    abstract public static function orWhere($key,$value=null,$op=null);
    abstract public static function insert($data);
    abstract public static function update($data);
}