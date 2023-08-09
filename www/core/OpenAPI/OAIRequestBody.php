<?php

namespace Core\OpenAPI;



class OAIRequestBody
{
    public $required = true;
    public ?OAIRequestBodyContent $content;

    public function __construct($content, $required = true)
    {
        $this->content = $content;
        $this->required = $required;
    }

    public function toJson()
    {
        return [
            "required" => $this->required,
            "content" => $this->content->toJson()
        ];
    }

}