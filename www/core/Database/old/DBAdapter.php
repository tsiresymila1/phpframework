<?php
namespace Core\Database;
use Core\Container\Container;
use Core\Utils\Logger;
use PDO;
use PDOException;

class DBAdapter {
    private static  $_instance;
    protected $config;
    public PDO $pdo;

    public function __construct()
    {

        $this->config = [
            "DB_CONNECTION" => env('DB_DRIVER','mysql'),
            "HOST"=> env('DB_HOST','localhost'),
            "DATABASE" => env('DB_DATABASE'),
            "USER"=> env('DB_USERNAME'),
            "PASSWORD" => env('DB_PASSWORD'),
            "PORT" => env('DB_PORT',3306)
        ];
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new DBAdapter();
        }
        return self::$_instance;
    }
    public static function Init()
    {
        $ins = self::instance();
        // try {
            $ins->pdo = new PDO($ins->config['DB_CONNECTION'] . ':host=' . $ins->config['HOST'] . ';port='.$ins->config['PORT'].';dbname=' . $ins->config['DATABASE'], $ins->config['USER'], $ins->config['PASSWORD'], array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
            $container = Container::instance();
            $container->register(static::class, static::class);
            $container->register(DB::class, DB::class);
        // } catch (PDOException $e) {
        //     Logger::error("Error : " . $e->getMessage() . "");
        //     throw new Exception($e->getMessage());
        // }
    }

    /**
     * @return PDO
     */
    protected function getPDO()
    {
        return $this->pdo;
    }

    /**
     * @param $query
     * @param $params
     * @param array $model
     * @return array
     */
    public function exec($query, &$params, $model = [])
    {
        $stmt = $this->getPDO()->prepare($query);
        Logger::addQuery($query,$params, "QUERY");
        try {
            $stmt->execute($params);
            $params = [];
            if(is_array($model)){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else{
                 return $stmt->fetchAll(PDO::FETCH_CLASS, $model);
            }

        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
            return array();
        }
    }

    /**
     * @param $query
     * @param $params
     * @param array $model
     * @return false|mixed
     */
    protected function execOne($query, &$params, $model = [])
    {
        $stmt = $this->getPDO()->prepare($query);
        Logger::addQuery($query,$params, "QUERY");
        try {
            $stmt->execute($params);
            $params = [];
                return $stmt->fetchObject(is_array($model) ? PDO::FETCH_COLUMN : $model);
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
            return false;
        }
    }

    /**
     * @param $query
     * @param $params
     * @return bool
     */
    public function execSilent($query, &$params)
    {
        $stmt = $this->getPDO()->prepare($query);
        Logger::addQuery($query,$params, "QUERY");
        try {
            $r = $stmt->execute($params);
            $params = [];
            return $r;

        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
        }
        return false;
    }
}
