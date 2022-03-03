<?php


namespace Core\OpenAPI;


class OAISchema
{
    public $ref;

    /**
     * @return array
     */
    public function toJson()
    {
        return array('$ref' => $this->ref);
    }

}