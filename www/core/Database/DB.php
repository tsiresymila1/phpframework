<?php

namespace Core\Database;

use Core\Utils\Logger;
use \Illuminate\Container\Container;
use \Illuminate\Database\Events\QueryExecuted;
use \Illuminate\Events\Dispatcher;
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Database\Schema\Builder as SchemaBuilder;



class DB
{

    private static $_instance;
    public static SchemaBuilder $schema;

    public static $migrationPath =  APP_PATH . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

    public static function Init()
    {
        $config = [
            "driver" => env("DB_DRIVER", "mysql"),
            "host" => env("DB_HOST", "localhost"),
            "database" => env("DB_DATABASE", "phpframework"),
            "username" => env("DB_USERNAME", "root"),
            "password" => env("DB_PASSWORD", ""),
            "port" => env("DB_PORT", 3306),
//             "charset" => "utf8",
//             "collation" => "utf8_unicode_ci",
//             "prefix" => "",
        ];
        $capsule = static::instance();
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $connection = $capsule->getConnection();
        $connection->enableQueryLog();
        $connection->setEventDispatcher(new Dispatcher(new Container()));
        $connection->listen(function (QueryExecuted $query) {
            Logger::addQuery($query->sql, $query->bindings);
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