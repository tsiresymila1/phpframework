<?php

namespace Core\OpenAPI;

class OAIPath
{
    public $path;
    private $method;
    private array $tag = [];
    private $summary;
    private $operationId;
    private $description;
    private array $produces = ['application/json'];
    private array $consumes = [];
    private array $parameters = [];
    private array $response = [];
    private array $security = [];

    /**
     * OAIPath constructor.
     * @param $p
     */
    public function __construct($p){
        $this->path = $p;
    }
    public function setDescription($d){
        $this->description = $d;
    }
    public function setMethod($m){
        if($m === 'POST'){
            $this->consumes[] = 'multipart/form-data';
        }
        $this->method = $m;
    }
    public function setSummary($s){
        $this->summary = $s;
    }

    public function setOperationId($o){
        $this->operationId = $o;
    }

    public function addTag($tag)
    {
        $this->tag[] = $tag;
    }

    public function addProduce($produce)
    {
        $this->produces[] = $produce;
    }

    public function addConsume($consume)
    {
        $this->consumes[] = $consume;
    }

    public function addParameters(array $p)
    {
        foreach ($p as $pe){
            $this->parameters[] = $pe->toJson();
        }
    }

    public function addSecuriry(array $p)
    {
        foreach ($p as $pe){
            $this->parameters[] = $pe->toJson();
        }
    }
    public function addResponses(array $r)
    {
        foreach ($r as $rep){
            $this->response[$rep->code] = $rep->toJson();
        }
    }
    public function toJson(){
        return array(
                "tags" => $this->tag,
                "summary" =>$this->summary,
                "description" => $this->description,
                "operationId" =>$this->operationId,
                "consumes"=>$this->consumes,
                "produces" =>$this->produces,
                "parameters" =>$this->parameters,
                "responses" =>$this->response,
                "security" =>$this->security
            );
    }
}
