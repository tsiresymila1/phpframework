<?php
namespace Core\Database;
use Core\Container\Container;
use Core\Utils\Logger;
use PDO;
use PDOException;
use RuntimeException;

class DBAdapter {
    private static $_instance;
    protected $config;
    public PDO $pdo;

    public function __construct()
    {
        // loading database config
        if (!file_exists(APP_PATH . 'config/database.php')) {
            throw new RuntimeException('File not found');
        }
        $this->config = require APP_PATH . 'config/database.php';
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
        try {
            $ins->pdo = new PDO($ins->config['DRIVER'] . ':host=' . $ins->config['HOST'] . ';dbname=' . $ins->config['DATABASE'], $ins->config['USER'], $ins->config['PASSWORD'], array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
            $container = Container::instance();
            $container->register(static::class, static::class);
            $container->register(DB::class, DB::class);
        } catch (PDOException $e) {
            Logger::error("Error : " . $e->getMessage() . "");
        }
    }

    protected function getPDO()
    {
        return $this->pdo;
    }

    public function exec($query, &$params, $model = [])
    {
        $qr = $this->getPDO()->prepare($query);
        Logger::log($query, "DATABASE QUERY");
        try {
            $qr->execute($params);
            $params = [];
            if(is_array($model)){
                return $qr->fetchAll(PDO::FETCH_BOTH);
            }
            else{
                return $qr->fetchAll(PDO::FETCH_CLASS, get_class($model));
            }

        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
            return array();
        }
    }

    protected function execOne($query,&$params, $model = [])
    {
        $qr = $this->getPDO()->prepare($query);
        Logger::log($query, "DATABASE QUERY");
        try {
            $qr->execute($params);
            $params = [];
            return $qr->fetchObject(is_array($model) ? PDO::FETCH_COLUMN : get_class($model));
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
            return false;
        }
    }

    public function execSilent($query, &$params, $model = [])
    {
        $qr = $this->getPDO()->prepare($query);
        Logger::log($query, "DATABASE QUERY");
        try {
            $r = $qr->execute($params);;
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
