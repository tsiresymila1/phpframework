<?php

namespace Core\Container;

use ReflectionMethod;
use ReflectionNamedType;
use Exception;
use ReflectionClass;

class Container implements ContainerInterface
{
    protected static $instance;
    protected $container = [];

    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function register($key, $class)
    {
        $this->container[$key] = $class;
    }

    public function getDependancies($class, $method)
    {

        $dependencies = [];
        $methodReflection = new ReflectionMethod($class, $method);
        $methodParams = $methodReflection->getParameters();
        foreach ($methodParams as $param) {
            $type = $param->getType();
            $className = $type->getName();
            if (array_key_exists($className, $this->container)) {
                array_push($dependencies, $this->container[$className]::instance());
            } else if ($type && $type instanceof ReflectionNamedType) {
                array_push($dependencies, $this->make($className));
            } else {
                $name = $param->getName();
                if (array_key_exists($name, $this->container)) {
                    array_push($dependencies, $this->container[$name]);
                } else {
                    if (!$param->isOptional()) {
                        throw new Exception("Can not resolve parameters");
                    }
                }
            }
        }
        return $dependencies;
    }

    public function resolve($class, $method)
    {
        $dependencies = $this->getDependancies($class, $method);
        $methodReflection = new ReflectionMethod($class, $method);
        if (!is_object($class)) {
            $initClass = $this->make($class);
        } else {
            $initClass = $this->callbackClass;
        }
        return $methodReflection->invoke($initClass, ...$dependencies);
    }

    public function make($class)
    {
        $classReflection = new ReflectionClass($class);
        $constructorParams = $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : [];
        $dependencies = [];
        foreach ($constructorParams as $constructorParam) {
            $type = $constructorParam->getType();
            $className = $type->getName();
            if (array_key_exists($className, $this->container)) {
                array_push($dependencies, $this->container[$className]);
            } else if ($type && $type instanceof ReflectionNamedType) {
                array_push($dependencies, $this->make($constructorParam->getClass()));
            } else {
                $name = $constructorParam->getName();
                if (!empty($parameters) && array_key_exists($name, $this->container)) {
                    array_push($dependencies, $this->container[$name]);
                } else {
                    if (!$constructorParam->isOptional()) {
                        throw new Exception("Can not resolve parameters");
                    }
                }
            }
        }
        return $classReflection->newInstance(...$dependencies);
    }
}
