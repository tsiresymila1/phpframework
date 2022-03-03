<?php

namespace Core\OpenAPI;

use Core\Http\Router;

class OpenApi
{

    public $swagger = "2.0";
    public $host = "";
    public $schemes = array(
        "http",
        "https"
    );
    public array $paths = array();
    public OAIInfos $info;
    public array $securityDefinition = [];
    public array $tags = array();

    protected static $instance;

    public function __construct()
    {
        $this->info = new OAIInfos();
    }

    public static function instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function loadRoute()
    {
        $routes = Router::GetRoutes();
        $routeTransformers = array_reduce(array_keys($routes), function ($prevArray, $method) use ($routes) {
            $nextArray = $routes[$method];
            foreach ($nextArray as $name => $item) {
                $path = $item->path;
                $paths = array();
                if (array_key_exists($path, $prevArray)) {
                    $paths = $prevArray[$path];
                }
                $paths[$method] = $item;
                $prevArray[$path] = $paths;
            }
            return $prevArray;
        }, []);
        foreach ($routeTransformers as $p => $route) {
            $oaiPath = array();
            foreach ($route as $meth => $r) {
                if (is_null($r->isAPI) || !$r->isAPI) {
                    continue;
                }
                $path = new OAIPath($p);
                if (gettype($r->action) == "string") {
                    $array = explode('@', $r->action);
                    $tag = str_replace('Controller', '',$array[0]);
                    if(!array_key_exists($tag,$this->tags)){
                        $this->tags[$tag] = array('name'=>$tag, 'description' =>'');
                    }
                    $path->addTag($tag);
                    $path->setOperationId($array[1]);
                }
                $path->addParameters($r->parameters);
                $path->addResponses($r->responses);
                $path->setMethod($meth);
                $path = $path->toJson();
                $oaiPath[strtolower($meth)] = $path;
            }
            if (sizeof($oaiPath) > 0) {
                $this->paths[$p] = $oaiPath;
            }
        }
    }

    /**
     * @return array
     */
    public static function getSPec()
    {
        $ins = self::instance();
        $ins->loadRoute();
        return array(
            'swagger' => $ins->swagger,
            'info' => $ins->info->toJSon(),
            'host' => $_SERVER['HTTP_HOST'],
            'tags' =>array_values($ins->tags),
            'schemes' => $ins->schemes,
            'paths' => $ins->paths,
            'securityDefinitions' => $ins->securityDefinition
        );
    }
}
