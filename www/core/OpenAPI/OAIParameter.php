<?php

namespace Core\OpenAPI;

class OAIParameter
{
    public $name;
    public $in;
    public $description;
    public $required;
    public $type;
    public $format;
    public OAISchema $schema;

    /**
     * OAIParameter constructor.
     * @param $name
     * @param string $in
     * @param string $description
     * @param bool $required
     * @param string $type
     * @param null $format
     */
    public function __construct($name, $in = "formData", $description = "", $required = true, $type = "string", $format = null)
    {
        $this->name = $name;
        $this->in = $in;
        $this->description = $description;
        $this->required = $required;
        $this->type = $type;
        $this->format = $format;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        $data = array(
            'name' => $this->name,
            'in' => $this->in,
            'description' => $this->description,
        );
        if(!is_null($this->required)){
            $data['required'] = $this->required;
        }
        if(!is_null($this->format)){
            $data['format'] = $this->format;
        }
        if(!is_null($this->type)){
            $data['type'] = $this->type;
        }
        return $data;
    }

}