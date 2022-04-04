<?php

namespace Core\Database;

use Core\Container\Container;
use Core\Utils\Logger;
use PDO;
use PDOException;
use RuntimeException;

class DB
{

    protected $config;
    protected PDO $pdo;
    private static ?DB $_instance = null;
    protected $conditions = [];
    protected $params = [];
    protected $parameters = [];
    protected $query = "";
    protected Model $model;

    public function __construct()
    {
        // loading database config
        if (!file_exists(APP_PATH . 'config/database.php')) {
            throw new RuntimeException('File not found');
        }
        $this->config = require APP_PATH . 'config/database.php';
    }

    public static function setQuery($query)
    {
        $ins = self::instance();
        $ins->query = $query;
    }

    public static function setConditions($conditions)
    {
        $ins = self::instance();
        $ins->conditions = $conditions;
    }

    public static function addParams($value)
    {
        $key = str_replace('.', '', uniqid('p',));
        $ins = self::instance();
        $ins->params[$key] = $value;
        return ':' . $key . ' ';
    }

    public function query($query)
    {
        $this->query = $query;
        return $this;
    }

    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public static function load(Model $model)
    {
        $ins = self::instance();
        $ins->model = $model;
        return $ins;
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
        } catch (PDOException $e) {
            Logger::error("Error : " . $e->getMessage() . "");
        }
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function insert(array $data)
    {
        $this->query = "INSERT INTO " . $this->model->getTable() . " SET ";
        $stringValue = [];
        foreach ($data as $k => $v) {
            $key = $this->addParams($v);
            $stringValue[] = $k . " =" . $key . "";
        }
        $date = date("Y-m-d H:i:s");
        $this->query .= implode(', ', $stringValue);

        if (!array_key_exists('createdAt', $data)) {
            $this->query .= ", " . "created_at='" . $date . "'";
        }
        if (!array_key_exists('updatedAt', $data)) {
            $this->query .= ", " . "updated_at='" . $date . "'";
        }
        $this->execSilent();
        $last_id = $this->getPDO()->lastInsertId();
        $this->{$this->model->getPrimaryKey()} = $last_id;
        return $last_id;
    }

    public function update($id = null)
    {
        if (!is_null($id)) {
            $this->groupedCondition(false);
            $key = $this->addParams($id);
            $this->query .= " WHERE " . $this->model->getPrimaryKey() . " =" . $key . " AND ";
        } else {
            $this->groupedCondition();
        }
        return $this->execSilent();
    }

    public function set($key, $value = null)
    {
        $this->conditions = [];
        $this->query = "UPDATE " . $this->model->getTable();
        if (gettype($key) == "array") {
            $stringValue = [];
            foreach ($key as $k => $v) {
                $key = $this->addParams($v);
                $stringValue[] = $k . " =" . $key . " ";
            }
        } else {
            $k = $this->addParams($value);
            $stringValue[] = $key . ' =' . $k;
        }
        $this->query .= " SET " . implode(',', $stringValue);
        return $this;
    }


    public function where($key, $value = null, $comparator = " =")
    {
        if (is_array($key)) {
            $where = array();
            foreach ($key as $k => $v) {
                $key = $this->addParams($v);
                $s = $k . $comparator . $key . '';
                array_push($where, $s);
            }
            $where = implode(' AND ', $where);
        } else {
            if (!is_null($value)) {
                $k = $this->addParams($value);
                $where = $key . $comparator . $k . '';
            } else {
                $where = $this->getPDO()->quote($key);
            }
        }
        array_push($this->conditions, array('AND' => $where));

        return $this;
    }

    public function orWhere($key, $value = null, $comparator = " =")
    {

        if (is_array($key)) {
            $where = array();
            foreach ($key as $k => $v) {
                $key = $this->addParams($v);
                array_push($where, $k . $comparator . $key . '');
            }
            $where = '(' . implode(' AND ', $where) . ')';
        } else {
            if (!is_null($value)) {
                $k = $this->addParams($value);
                $where = $key . $comparator . $k . '';
            } else {
                $where = $this->getPDO()->quote($key);
            }
        }
        array_push($this->conditions, array('OR' => $where));
        return $this;
    }

    private function exec()
    {
        $qr = $this->getPDO()->prepare($this->query);
        Logger::log($this->query, "DATABASE QUERY");
        try {
            $qr->execute($this->params);
            $this->params = [];
            return $qr->fetchAll(PDO::FETCH_CLASS, get_class($this->model));
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
            return array();
        }
    }

    private function execOne()
    {
        $qr = $this->getPDO()->prepare($this->query);
        Logger::log($this->query, "DATABASE QUERY");
        try {
            $qr->execute($this->params);
            $this->params = [];
            return $qr->fetchObject(get_class($this->model));
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
            return false;
        }
    }

    public function execSilent()
    {
        $qr = $this->getPDO()->prepare($this->query);
        Logger::log($this->query, "DATABASE QUERY");
        try {
            $r = $qr->execute($this->params);;
            $this->params = [];
            return $r;

        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            if (defined('DEBUG') && DEBUG == true) {
                throw new PDOException($e->getMessage());
            }
        }
        return false;
    }

    public function get()
    {
        $this->groupedCondition();
        return $this->exec();
    }

    public function getOne()
    {
        $this->groupedCondition();
        return $this->execOne();
    }

    public function count()
    {
        $this->groupedCondition();
        return count($this->exec());
    }

    private function groupedCondition($withClause = true)
    {
        $whereQuery = "";
        foreach ($this->conditions as $index => $value) {
            foreach ($value as $k => $v) {
                $whereQuery .= $v;
                if ($index + 1 != sizeof($this->conditions)) {
                    $whereQuery .= " " . $k . " ";
                }
            }
        }
        if ($whereQuery != "") {
            if ($withClause) {
                $this->query .= " WHERE " . $whereQuery;
            } else {
                $this->query .= " " . $whereQuery;
            }
        };
    }
}
