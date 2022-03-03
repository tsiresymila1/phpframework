<?php


namespace Core\OpenAPI;


class OAIResponse
{
    public $code;
    public $description;
    public OAISchema $schema;

    /**
     * @return array
     */
    public function toJson()
    {
        return array(
            'description' => $this->description,
            'schema' => $this->schema->toJson()
        );
    }

}
