<?php

namespace Core\OpenAPI;

use ReflectionClass, ReflectionProperty;

class OAISecurity
{

    public $tag;
    public $key;
    public $type;
    public $name;
    public $in;
    public $scheme;

    public $bearerFormat;
    

    public function __construct($tag, $type, $name, $key = null, $in = null, $scheme = null)
    {
        $this->tag = $tag;
        $this->type = $type;
        $this->name = $name;
        $this->key = $key;
        $this->in = $in;
        $this->scheme = $scheme;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        $data = [];
        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            if ($property->name != "tag" && $this->{$property->name} != null) {
                $data[$property->name] = $this->{$property->name};
            }
        }
        return [
            $this->tag => $data
        ];
    }

    public function toAccess($data = [])
    {
        return [
            $this->tag => $data
        ];
    }
}
