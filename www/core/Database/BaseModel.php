<?php

namespace Core\Database;
use DateTime;
use ReflectionObject, ReflectionProperty;

class BaseModel extends ModelAbstract{


    protected ?DateTime $createdAt;
    protected ?DateTime $updatedAt;
    protected ?DateTime $deletedAt;


    private  QueryBuilder $queryBuilder;
    private static $_instance;

    protected  $_table = null;
    protected $primaryKey = 'id';


    public function __construct( )
    {
        $this->queryBuilder = new QueryBuilder();
        $this->queryBuilder->model(static::class);
    }

    /**
     * @param $prop
     * @return string
     */
    public function __get($prop){
        if(method_exists(static::class,$prop)){
            return $this->{$prop}();
        }
        else{
            return  $this->{$prop};
        }
    }


    /**
     * @param $prop
     * @param $val
     */
    public function __set($prop, $val) {
        if($prop == 'created_at'){
            try {
                $this->createdAt = new DateTime($val);
            } catch (\Exception $e) {
                $this->createdAt = $val;
            }
        }else if($prop == 'updated_at'){
            try {
                $this->updatedAt = new DateTime($val);
            } catch (\Exception $e) {
                $this->updatedAt = $val;
            }
        }
        else if($prop == 'deleted_at'){
            try {
                $this->deletedAt = new DateTime($val);
            } catch (\Exception $e) {
                $this->deletedAt = $val;
            }
        }
        else{
            $this->{$prop} = $val;
        }
    }

    /**
     * @return static
     */
    private static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    private static function destruct(){
        self::$_instance = null ;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        if(is_null($this->_table)){
            $name = $this->endc(explode('\\', get_class($this)));
            $this->_table = strtolower($name);
        }
        return $this->_table;
    }

    /**
     * @param $id
     * @param null $primary
     * @return array
     */
    public static function find($id, $primary = null)
    {
        static::destruct();
        if (is_null($primary)) {
            $primary = static::instance()->primaryKey;
        }
        if (is_array($id)) {
            foreach ($id as $ide) {
                static::instance()->queryBuilder->orWhere($primary,$ide);
            }
        } else {
            static::instance()->queryBuilder->where($primary,$id);
        }
        return static::instance()->queryBuilder->select('*')->from(static::instance()->getTable())->get()->rows();
    }

    /**
     * @param $id
     * @param null $primary
     * @return mixed
     */
    public static function findOne($id, $primary=null)
    {
        static::destruct();
        $ins = self::instance();
        if (is_null($primary)) {
            $primary = $ins->primaryKey;
        }
        return $ins->queryBuilder->select('*')->from($ins->getTable())->where($primary,$id)->get()->first();
    }

    /**
     * @param $key
     * @param null $value
     * @param string $comparator
     * @return mixed
     */
    public static function findOneBy($key, $value = null, $comparator = "=")
    {
        static::destruct();
        $ins = self::instance();
        return $ins->queryBuilder->select('*')->from($ins->getTable())->where($key, $value, $comparator)->get()->first();
    }

    /**
     * @param $key
     * @param $value
     * @param string $comparator
     * @return array
     */
    public static function findBy($key, $value, $comparator = "=")
    {
        static::destruct();
        $ins = self::instance();
        return $ins->queryBuilder->select('*')->from($ins->getTable())->where($key, $value, $comparator)->get()->rows();
    }

    /**
     * @return array
     */
    public static function findAll()
    {
        return self::get('*')->rows();
    }

    /**
     * @param $key
     * @param null $value
     * @param null $op
     * @return QueryBuilder
     */
    public static function where($key, $value=null, $op=null)
    {
        static::destruct();
        $ins = self::instance();
        return $ins->queryBuilder->into($ins->getTable())->where($key,$value,$op);
    }

    /**
     * @param $key
     * @param null $value
     * @param null $op
     * @return QueryBuilder
     */
    public static function orWhere($key, $value=null, $op=null)
    {
        static::destruct();
        $ins = self::instance();
        return $ins->queryBuilder->into($ins->getTable())->orWhere($key,$value,$op);
    }

    /**
     * @param string $columns
     * @return QueryBuilder
     */
    private static function get($columns="*")
    {
        static::destruct();
        $ins = self::instance();
        return  $ins->queryBuilder->select($columns)->from( $ins->getTable())->get();
    }

    /**
     * @param $data
     * @return QueryBuilder
     */
    public static function insert($data)
    {
        static::destruct();
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $times = [
            'created_at' => $now,
            'updated_at' => $now,
            'soft_deleted' => 0
        ];
        $values = array_merge(array_diff($data, $times), array_diff($times, $data));
        return static::instance()->queryBuilder->into(static::instance()->getTable())->insert($values);
    }
    

    public  function save(){
        $data = get_object_vars($this);
        $reflectdata = array_reduce((new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC), function($data, $elm){
            $data[] = $elm->name;
            return $data;
        },[]);

        $values = array_reduce(array_keys($data), function($d, $e) use($reflectdata, $data){
            if(in_array($e,$reflectdata)){
                $d[$e] =  $data[$e];
            }
            return $d;
        },[]);
        return static::insert($values)->get();
    }

    /**
     * @param $data
     * @return QueryBuilder
     */
    public static function update($data=null)
    {
        static::destruct();
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $times = [
            'updated_at' => $now
        ];
        if(is_array($data)){
            $values = array_merge(array_diff($data, $times), array_diff($times, $data));
            return static::instance()->queryBuilder->update(static::instance()->getTable())->set($values);
        }else{
            return static::instance()->queryBuilder->update(static::instance()->getTable());
        }

    }

    /**
     * @param null $id
     * @return QueryBuilder
     */
    public static function delete($id=null){
        static::destruct();
        if(!is_null($id)){
            return static::instance()->queryBuilder->delete()->into(static::instance()->getTable())->where(static::instance()->primaryKey, $id);
        }
        else{
            return static::instance()->queryBuilder->delete()->into(static::instance()->getTable());
        }
    }

    /**
     * @param null $id
     * @return QueryBuilder
     */
    public static function softDelete($id=null){
        $now = (new DateTime())->format('YYYY-MM-DD HH:MM:SS');
        $data = [
            'daleted_at' => $now
        ];
        if(!is_null($id)){
            return static::instance()->queryBuilder->update(static::instance()->getTable())->set($data)->where(static::instance()->primaryKey, $id);
        }
        else{
            return static::instance()->queryBuilder->update(static::instance()->getTable())->set($data);
        }
    }

    // ORM

    /**
     * @param  $model
     * @param null $foreign_key
     * @param null $local_key
     * @return mixed
     */
    protected function hasOne($model, $foreign_key = null, $local_key = null)
    {

        $m = new $model();
        if (is_null($foreign_key)) {
            $foreign_key = strtolower($m->getTable()) . '_' . $m->primaryKey;
        }
        if (is_null($local_key)) {
            $local_key = $m->primaryKey;
        }
        return $m->findOneBy($local_key, $this->{$foreign_key});
    }

    protected function belongsTo($model, $local_key = null, $primary_key = null)
    {

        $m = new $model;
        if (is_null($local_key)) {
            $local_key = strtolower($m->getTable()) . '_' . $m->primaryKey;
        }
        if (is_null($primary_key)) {
            $primary_key = $m->primaryKey;
        }
        return $model::findOneBy($primary_key, $this->{$local_key});
    }

    /**
     * @param mixed $model
     * @param null $foreign_key
     * @param null $local_key
     * @param null $through
     * @return array
     */
    protected function hasMany($model, $foreign_key = null, $local_key = null, $through = null)
    {
        $m = new $model;
        if (is_null($foreign_key)) {
            $foreign_key = strtolower($m->getTable()) . '_' . $m->primaryKey;
        }
        if (is_null($local_key)) {
            $local_key = $this->primaryKey;
        }
        if (!is_null($through)) {
            $local_key = strtolower($this->getTable()) . '_' . $this->primaryKey;
            $throughModel = new $through;
            $data = $throughModel->findBy($local_key, $this->{$this->primaryKey});
            $localKeysPrimary = array_reduce($data, function ($prev, $val) use ($foreign_key) {
                if (isset($val[$foreign_key])) {
                    $prev[] = $val[$foreign_key];
                }
                return $prev;
            }, []);
            return $m->find($localKeysPrimary, $foreign_key);
        } else {
            return $m->findBy($foreign_key, $this->{$local_key});
        }
    }

     public function toArray(){
        $class = new ReflectionObject($this);
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        $data = [];
        foreach($properties as $property){
            $data[$property->name] = $this->{$property->name};
        }
        return $data;
     }

}


