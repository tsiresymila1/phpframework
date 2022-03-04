<?php


namespace Core\OpenAPI;


class OAIResponse
{
    public $code;
    public $description;
    public OAISchema $schema;

    public function __construct($code = 200, $description = 'Response 200', $schema = null)
    {
        $this->code = $code;
        $this->description = $description;
        $this->$schema = $schema;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        $data = array(
            'description' => $this->description,
            'schema' => $this->schema->toJson()
        );
        if (!is_null($this->schema)) {
            $data['schema'] = $this->schema;
        }
        return $data;
    }

}
