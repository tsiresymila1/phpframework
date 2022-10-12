<?php

namespace Core\Database;

class DB
{
    protected QueryBuilder $queryBuilder;

    private static $_instance;

    public function __construct(QueryBuilder $queryBuilder )
    {
        $this->queryBuilder = $queryBuilder;
    }

    private static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new static(new QueryBuilder());
        }
        return self::$_instance;
    }

    public static function table($table){
        return static::instance()->queryBuilder->into($table);
    }

}
