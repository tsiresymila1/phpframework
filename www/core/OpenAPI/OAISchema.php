<?php


namespace Core\OpenAPI;


class OAISchema
{
    public $name;

    public $type;

    public $properties = [];

    public $required = [];

    public function __construct($name,$type = "object",$properties = [], $required=[])
    {
        $this->name = $name;
        $this->type = $type;
        $this->properties = $properties;
        $this->required = $required;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        return [
            $this->name => [
                "type" => $this->type,
                "properties" => $this->properties,
                "required" => $this->required
            ]
        ];
    }

}