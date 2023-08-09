<?php

namespace Core\Database\Eloquent;

use Core\Utils\Logger;
use Illuminate\Container\Container;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Database\Schema\Builder as SchemaBuilder;


class EloquentDB
{

    private static $_instance;
    public static SchemaBuilder $schema;

    public static function Init()
    {
        $config = [
            "driver" => env("DB_DRIVER", "mysql"),
            "host" => env("DB_HOST", "localhost"),
            "database" => env("DB_DATABASE"),
            "username" => env("DB_USERNAME"),
            "password" => env("DB_PASSWORD"),
            "port" => env("DB_PORT", 3306),
            // "charset" => "utf8",
            // "collation" => "utf8_unicode_ci",
            // "prefix" => "",
        ];
        $capsule = static::instance();
        $capsule->addConnection($config);
        $capsule->getConnection()->enableQueryLog();
        $capsule->setAsGlobal();
        $capsule->getConnection()->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->getConnection()->listen(function (QueryExecuted $query){
            Logger::addQuery($query->sql,$query->bindings);
        });

        $capsule->bootEloquent();

        self::$schema = $capsule->schema();

    }

    /**
     * @return Capsule 
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Capsule();
        }
        return self::$_instance;
    }
}