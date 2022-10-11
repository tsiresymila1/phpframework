<?php

namespace Core\Database;

use ReflectionClass;
use ReflectionProperty;
use Core\Utils\DocBlock;

class Migration
{

    public function __construct()
    {

        $models = array_filter(scandir(APP_PATH . "Model"), function ($ar) {
            return $ar != "." && $ar != "..";
        });
        $arr = array_reduce($models, function ($prev, $next) {
            $prev[] = "App\\Model\\" . str_replace(['.php'], "", $next);
            return $prev;
        }, []);

        foreach ($arr as $model) {
            echo "\n".$model."\n";
            $ins = new $model();
            $reflect = new ReflectionClass($ins);
            $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
            foreach ($props as $prop) {
                $doc = $prop->getDocComment();
                if($doc){
                    $bloc = new DocBlock($doc);
                    echo "\t $prop->name => ".json_encode($bloc->all_params)."\n";
                }
            }
        }
    }
}
