<?php

namespace Core\OpenAPI;

use Core\Http\Router;
use Core\OpenAPI\OAISecurity;

class OpenApi
{

    public $openapi = "3.0.3";

    public $swagger = "2.0.0";
    public $host = "";
    public $schemes = array(
        "http",
        "https"
    );
    public $schema = [];
    public array $paths = array();
    public OAIInfos $info;
    public array $tags = array();
    public $bearerSecurity;

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

    public $securitySchema = [];

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
                    $tag = str_replace('Controller', '', $array[0]);
                    if (!array_key_exists($tag, $this->tags)) {
                        $this->tags[$tag] = array('name' => $tag, 'description' => '');
                    }
                    $path->addTag($tag);
                    $path->setOperationId($array[1]);
                }
                $path->addParameters($r->parameters);
                $path->addResponses($r->responses);
                $path->setMethod($meth);
                $path->addSecurity($r->security);
                if ($r->requestBody) {
                    $path->setRequestBody($r->requestBody);
                }
                $this->securitySchema = array_unique(array_merge($this->securitySchema, $r->security), SORT_REGULAR);
                $path = $path->toJson();
                $oaiPath[strtolower($meth)] = $path;
            }
            if (sizeof($oaiPath) > 0) {
                $this->paths[str_replace('?', '', $p)] = $oaiPath;
            }
        }
    }

    public static function addSchema($schema)
    {
        $ins = self::instance();
        if (is_array($schema)) {
            $ins->schema = array_merge($ins->schema, $schema);
        } else {
            $ins->schema[] = $schema;
        }
    }

    /**
     * @return array
     */
    public static function getSPec()
    {
        $ins = self::instance();
        $ins->loadRoute();
        $security = array_map(function ($sec) {
            return $sec->toJson();
        }, $ins->securitySchema);

        $schema = array_reduce($ins->schema, function ($prevArray, $sc) {
            return array_merge($prevArray, $sc->toJson());
        }, []);
        $data = array(
            'openapi' => $ins->openapi,
            'info' => $ins->info->toJSon(),
            'host' => $_SERVER['HTTP_HOST'],
            'tags' => array_values($ins->tags),
            'schemes' => $ins->schemes,
            'paths' => $ins->paths,
            'securityDefinitions' => sizeof($security) > 0 ? $security[0] : [],
            'components' => [
                'schemas' => $schema,
                'securitySchemes' => sizeof($security) > 0 ? $security[0] : []
            ],
        );

        return $data;
    }
}