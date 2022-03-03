<?php


namespace Core\OpenAPI;


class OAIInfos
{
    public $description;
    public $version;
    public $title;

    public function __construct($version="1.0.0", $description="",$title="PHP FRAMEWORK API SWAGGER")
    {
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
    }

    public function toJSon()
    {
        return array(
            "description" => $this->description,
            "version" => $this->version,
            "title" => $this->title,
            "license" => array(
                "name" => "Apache 2.0",
                "url" => "http://www.apache.org/licenses/LICENSE-2.0.html"
            )
        );
    }
}