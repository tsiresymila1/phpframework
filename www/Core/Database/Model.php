<?php

namespace Core\Database;
use DateTime;
use ReflectionClass;

class Model
{

    protected $table = null;

    protected ?DateTime $createdAt;
    protected ?DateTime $updatedAt;
    protected ?DateTime $deletedAt;

    //ORM
    protected $primary_key = 'id';

    private function snakeCase($string, $us = "_")
    {
        return strtolower(preg_replace('/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $us, $string));
    }

    private function endc($array)
    {
        return end($array);
    }

    public function save()
    {
        $class = new ReflectionClass($this);
        $propsToImplode = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $propsToImplode[$this->snakeCase($propertyName)] = $this->{$propertyName};
        }
        DB::load($this);
        if (property_exists(static::class, $this->primary_key)) {
            $data = DB::instance()->update($this->${$this->primary_key});
        } else {
            $data = DB::instance()->insert($propsToImplode);
            $this->{$this->primary_key} = $data;
        }

        return $data;
    }

    public function delete()
    {
        if (property_exists($this, $this->primary_key)) {
            $key = DB::addParams($this->{$this->primary_key});
            DB::setQuery("DELETE FROM " . $this->table . " WHERE " . $this->primary_key . '=' . $key);
            return DB::instance()->execSilent();
        }
        return false;
    }

    /**
     * @return null
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    protected function hasOne($model, $foreign_key = null, $local_key = null)
    {
        if (is_null($foreign_key)) {
            $foreign_key = strtolower($model) . '_' . $this->primary_key;
        }
        if (is_null($local_key)) {
            $local_key = $this->primary_key;
        }
        $m = new $model;
        return $m->findOneBy($local_key, $this->{$foreign_key});
    }

    protected function hasMany($model, $foreign_key = null, $local_key = null, $through = null)
    {
        if (is_null($foreign_key)) {
            $foreign_key = strtolower($model) . '_' . $this->primary_key;
        }
        if (is_null($local_key)) {
            $local_key = $this->primary_key;
        }
        $m = new $model;
        if (!is_null($through)) {
            $throughModel = new $through;
            $data = $throughModel->findBy($local_key, $this->{$local_key});
            $localKeys = array_reduce($data, function ($prev, $val) use ($foreign_key) {
                if (isset($val[$foreign_key])) {
                    $prev[] = $val[$foreign_key];
                }
                return $prev;
            }, []);
            return $m->find($localKeys, $foreign_key);
        } else {
            return $m->findBy($foreign_key, $this->{$local_key});
        }
    }

    //END ORM

    public function find($ids, $primary = null)
    {
        if (is_null($primary)) {
            $primary = $this->primary_key;
        }
        DB::setConditions([]);
        if (is_array($ids)) {
            $whereQuery = $primary . "=";
            foreach ($ids as $id) {
                $key = DB::addParams($id);
                if ($id == $this->endc($ids)) {
                    $whereQuery .= ' ' . $primary . ' =' . $key . ' OR ';
                } else {
                    $whereQuery .= ' ' . $primary . ' =' . $key . ' ';
                }

            }
        } else {
            $key = DB::addParams($ids);
            $whereQuery = "'.$primary.' =" . $key;
        }
        DB::setQuery("SELECT * FROM " . $this->table . " WHERE " . $whereQuery);
        return DB::instance()->get();
    }

    private function _find($key, $value = null, $comparator = " =")
    {
        if (is_array($key)) {
            $whereQuery = implode(" AND ", array_map(function ($k, $v) {
                $key = DB::addParams($v);
                return $k . '=' . $key . '';
            }, array_keys($key), $key));
        } else {

            $k = DB::addParams($value);
            $whereQuery = $key . $comparator . $k;
        }
        DB::setConditions([]);
        DB::setQuery("SELECT * FROM " . $this->table . " WHERE " . $whereQuery);
    }

    public function findOneBy($key, $value = null, $comparator = "=")
    {
        $this->_find($key, $value, $comparator);
        return DB::instance()->getOne();
    }

    public function findBy($key, $value, $comparator = "=")
    {
        $this->_find($key, $value, $comparator);
        return DB::instance()->get();
    }

    public function findAll()
    {
        DB::setQuery("SELECT * FROM " . $this->table . " ");
        return $this;
    }


}
