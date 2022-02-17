<?php


namespace Core\Container;


interface ContainerInterface
{
    /**
     * instance
     *
     * @return Container
     */
    public static function instance();
    /**
     * register
     *
     * @param mixed key
     * @param mixed class
     *
     * @return void
     */
    public function register($key, $class);
    /**
     * resolve
     *
     * @param mixed class
     * @param mixed method
     *
     * @return mixed
     */
    public function resolve($class, $method);
}
