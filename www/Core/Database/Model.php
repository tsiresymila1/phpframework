<?php

namespace Core\Database;

use Core\Utils\Logger;
use DateTime;
use PDO;
use PDOException;
use ReflectionClass;

class Model
{

    protected  $table;
    protected static  $tablename;
    protected  $query = "";
    protected  $conditions = [];
    protected  $db;

    public DateTime $createdAt;
    public DateTime $updatedAt;
    public DateTime $deletedAt;
    public $useSoftDeletes = true;

    public function __construct()
    {
        $class = $this->endc(explode('\\', get_class($this)));
        self::$tablename = str_replace(["model", "\\", "models"], "", strtolower($class)) . "s";
        $this->db = DB::instance();

        $reflect = new ReflectionClass(get_class($this));
        // $sql = "CREATE TABLE " . self::$tablename . " IF NOT EXISTS ( id INT(11) PRIMARY KEY , ";
        // foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $p) {
        //     $name = $p->getName();
        //     $document = $p->getDocComment();
        //     if ($document) {
        //         try {
        //             $parser = new DocBlock($document);
        //             $parser->parse_block();
        //             $column = $parser->getColumn();
        //         } catch (\Exception $e) {
        //         }
        //     } else if ($p->getType()) {
        //         $column  = $p->getType()->getName();
        //     } else {
        //         $column = "text";
        //     }
        //     $sql .= $name . " " . $column . ",";
        // }
        // $sql .= ")";
    }

    protected function endc($array)
    {
        return end($array);
    }


    public  function query($query)
    {
        $this->query = $query;
        return  $this;
    }

    public  function find($ids)
    {
        $this->conditions = [];
        if (gettype($ids) == 'array') {
            $conditionsString = "id=";
            $conditionsString .= implode(' AND id=', $ids);
        } else {
            $conditionsString = "id=" . $ids;
        }
        $this->query = "SELECT * FROM " . self::$tablename . " WHERE " . $conditionsString;
        return $this->get();
    }

    public function findOneBy($key, $value = null, $comparator = "=")
    {
        if (gettype($key) == 'array') {
            $conditionsString = implode(" AND ", array_map(function ($k, $v) {
                return $k . '=\'' . $v . '\'';
            }, array_keys($key), $key));
        } else {
            $conditionsString = $key . $comparator . $value;
        }
        $this->conditions = [];
        $this->query = "SELECT * FROM " . self::$tablename . " WHERE " . $conditionsString;
        return $this->getOne();
    }

    public function findBy($key, $value, $comparator = "=")
    {
        $this->conditions = [];
        $this->query = "SELECT * FROM " . self::$tablename . " WHERE " . $key . $comparator . $value;
        return $this->get();
    }
    public  function findAll()
    {
        $this->query = "SELECT * FROM " . self::$tablename . " ";
        return  $this;
    }

    public  function insert(array $data)
    {
        $this->query = "INSERT INTO " . self::$tablename . " SET ";
        $stringValue = [];
        foreach ($data as $k => $v) {
            $stringValue[] = $k . "='" . $v . "'";
        }
        $date = date("Y-m-d H:i:s");
        $this->query .= implode(',', $stringValue);

        if (!array_key_exists('createdAt', $data)) {
            $this->query .= "," . "created_at='" . $date . '\'';
        }
        if (!array_key_exists('updatedAt', $data)) {
            $this->query .= "," . "updated_at='" . $date . '\'';
        }
        return $this->execSilent();
    }

    public  function update($id = null)
    {

        if (!is_null($id)) {
            $this->groupedCondition(false);
            $this->query .= " WHERE id=" . $id . " AND ";
        } else {
            $this->groupedCondition();
        }
        return $this->execSilent();
    }

    public function set($key, $value = null)
    {
        $this->conditions = [];
        $this->query = "UPDATE " . self::$tablename;
        if (gettype($key) == "array") {
            $stringValue = [];
            foreach ($key as $k => $v) {
                $stringValue[] = $k . "='" . $v . "'";
            }
        } else {
            $stringValue = array($key => $value);
        }
        $this->query .= " SET " . implode(',', $stringValue);
        return $this;
    }

    public  function innerJoin(int $id = null)
    {
    }

    public  function leftJoin(int $id = null)
    {
    }
    public  function withDeleted()
    {
    }

    public  function where($key, $value = null, $comparator = "=")
    {
        if (gettype($key) == "array") {
            $where = array();
            foreach ($key as $k => $v) {
                array_push($where, $k . $comparator . '\'' . $v . '\'');
            }
            $where = implode(' AND ', $where);
        } else {
            if (!is_null($value)) {
                $where = $key . $comparator . '\'' . $value . '\'';
            } else {
                $where = $key;
            }
        }
        array_push($this->conditions, array('AND' => $where));

        return  $this;
    }

    public  function orWhere($key, $value = null, $comparator = "=")
    {

        if (gettype($key) == "array") {
            $where = array();
            foreach ($key as $k => $v) {
                array_push($where, $k . $comparator . '\'' . $v . '\'');
            }
            $where = '(' . implode(' AND ', $where) . ')';
        } else {
            if (!is_null($value)) {
                $where = $key . $comparator . '\'' . $value . '\'';
            } else {
                $where = $key;
            }
        }
        array_push($this->conditions, array('OR' => $where));
        return  $this;
    }

    protected function execSilent()
    {
        $qr = $this->db->getPDO()->prepare($this->query);
        Logger::log($this->query, "DATABASE QUERY");
        try {
            return $qr->execute();
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
        }
        return false;
    }

    protected function exec()
    {
        $qr = $this->db->getPDO()->prepare($this->query);
        Logger::log($this->query, "DATABASE QUERY");
        try {
            $qr->execute();
            return $qr->fetchAll(PDO::FETCH_CLASS, get_class($this));
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            return false;
        }
    }

    protected function execOne()
    {
        $qr = $this->db->getPDO()->prepare($this->query);
        Logger::log($this->query, "DATABASE QUERY");
        try {
            $qr->execute();
            $data = $qr->fetchObject(get_class($this));
            return $data;
        } catch (PDOException  $e) {
            Logger::error($e->getMessage(), "DATABASE ERROR");
            return false;
        }
    }

    public  function get()
    {
        $this->groupedCondition();
        return $this->exec();
    }

    public  function getOne()
    {
        $this->groupedCondition();
        return $this->execOne();
    }

    public function count()
    {
        $this->groupedCondition();
        return count($this->exec());
    }

    protected function groupedCondition($withClause = true)
    {
        $conditionsString = "";
        foreach ($this->conditions as $index => $value) {
            foreach ($value as $k => $v) {
                $conditionsString .= $v;
                if ($index + 1 != sizeof($this->conditions)) {
                    $conditionsString .= " " . $k . " ";
                }
            }
        }
        if ($conditionsString != "") {
            if ($withClause) {
                $this->query .= " WHERE " . $conditionsString;
            } else {
                $this->query .= " " . $conditionsString;
            }
        };
    }
}
