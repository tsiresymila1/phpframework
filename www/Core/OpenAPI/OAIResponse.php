<?php


namespace Core\OpenAPI;


class OAIResponse
{
    public int $code;
    public string $description;
    public ?OAISchema $schema = null;

    /**
     * OAIResponse constructor.
     * @param int $code
     * @param string $description
     * @param OAISchema|null $schema
     */
    public function __construct($code = 200, $description = 'Response 200', $schema = null)
    {
        $this->code = $code;
        $this->description = $description;
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function toJson()
    {
        $data = array(
            'code' => $this->code,
            'description' => $this->description,
        );
        if (!is_null($this->schema)) {
            $data['schema'] = $this->schema;
        }
        return $data;
    }

}
