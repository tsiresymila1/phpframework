<?php
namespace Core\OpenAPI;
class OAIRequestBodyContent
{
    public $type;
    public $schemaRef;

    public function __construct($type, $schemaRef)
    {
        $this->type = $type;
        $this->schemaRef = $schemaRef;
    }

    public function toJson()
    {
        return [
            $this->type => [
                "schema" => [
                    '$ref' => "#/components/schemas/{$this->schemaRef}"
                ]
            ]
        ];
    }
}