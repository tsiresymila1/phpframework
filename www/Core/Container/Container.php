<?php

namespace Core\Container;

use ReflectionException;
use ReflectionFunction;
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

    /**
     * register
     *
     * @param mixed key
     * @param mixed class
     *
     * @return void
     */
    public function register($key, $class)
    {
        $this->container[$key] = $class;
    }

    /**
     * getDependancies
     *
     * @param $class
     * @param null $method
     * @param array $params
     * @param bool $isfunction
     * @return array
     * @throws ReflectionException
     */
    public function getDependencies($class, $method=null, $params = [],$isfunction=false)
    {
        $dependencies = [];
        if($isfunction){
            $methodReflection = new ReflectionFunction($class);
        }
        else{
            $methodReflection = new ReflectionMethod($class, $method);
        }
        $methodParams = $methodReflection->getParameters();
        foreach ($methodParams as $param) {
            $type = $param->getType();
            $name = $param->getName();
            if (is_null($type) && array_key_exists($name,$params)) {
                array_push($dependencies, $params[$name]);
            } else {
                if(is_null($type)){
                    array_push($dependencies, null);
                    continue;
                }
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
                            array_push($dependencies, null);;
                        }
                    }
                }
            }
        }
        return $dependencies;
    }

    /**
     * resolve
     *
     * @param $class
     * @param null $method
     * @param array $params
     * @param bool $isfunction
     * @return mixed
     * @throws ReflectionException
     */
    public function resolve($class, $method=null, $params = [],$isfunction=false)
    {
        $dependencies = $this->getDependencies($class, $method, $params,$isfunction);
        if($isfunction){
            return $class(...$dependencies);
        }
        else{
            $methodReflection = new ReflectionMethod($class, $method);
            if (!is_object($class)) {
                $initClass = $this->make($class, [], $params);
            } else {
                $initClass = $this->callbackClass;
            }
            return $methodReflection->invoke($initClass, ...$dependencies);
        }
    }

    /**
     * make
     *
     * @param mixed class
     * @param mixed parents
     * @param mixed params
     *
     * @return mixed
     * @throws Exception
     */
    public function make($class, $parents = [], $params = [])
    {
        $classReflection = new ReflectionClass($class);
        $constructorParams = $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : [];
        $dependencies = [];
        foreach ($constructorParams as $constructorParam) {
            $type = $constructorParam->getType();
            $name = $constructorParam->getName();;
            if (is_null($type) && array_key_exists($name,$params)) {
                array_push($dependencies, $params[$name]);
            } else {
                $className = $type->getName();
                if (array_key_exists($className, $this->container)) {
                    $this->handleCircularReference($className, $parents);
                    $parents[] = $className;
                    array_push($dependencies, $this->container[$className]);
                } else if ($type && $type instanceof ReflectionNamedType) {
                    $className = $constructorParam->getClass();
                    $this->handleCircularReference($className, $parents);
                    $parents[] = $className;
                    array_push($dependencies, $this->make($className, $parents));
                } else {
                    if (!empty($this->container) && array_key_exists($name, $this->container)) {
                        array_push($dependencies, $this->container[$name]);
                    } else {
                        if (!$constructorParam->isOptional()) {
                            throw new Exception("Can not resolve parameters");
                        }
                    }
                }
            }
        }
        return $classReflection->newInstance(...$dependencies);
    }

    /**
     * handleCircularReference
     *
     * @param mixed className
     * @param mixed classes
     *
     * @return void
     * @throws Exception
     */
    public function handleCircularReference($className, $classes)
    {
        if (in_array($className, $classes)) {
            throw new Exception("Circular reference found in dependency injection");
        }
    }
}
