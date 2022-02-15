<?php


namespace Core\Container;


interface ContainerInterface
{
    public static function instance();
    public function register($key, $class);
    public function resolve($class, $method);
}
