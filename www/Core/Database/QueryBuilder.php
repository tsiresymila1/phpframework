<?php
namespace Core\Database;

class QueryBuilder {

    public $table = "";
    public $columns = "*";
    public  $whereClose = [];
    public  $orWhereClose = [];
    public  $setClose = [];
    private $insertData = [];
    private $params = [];
    private $type = "SELECT";
    protected $isOr = false;
    protected $isSet = false;
    protected $model = [];
    private $data = [];
    private $adapter;
    private $joins = [];

    public function __construct(){
        $this->adapter = DBAdapter::instance();
    }


    /**
     * @param $value
     * @return string
     */
    private  function valueToParams($value)
    {
        $key = str_replace('.', '', uniqid('p',));
        $this->params[$key] = $value;
        return ':' . $key . ' ';
    }

    /**
     * @param $arr
     * @return bool
     */
    private function isSequentialArray($arr) {
        return array_values($arr) !== $arr;
    }

    /**
     * @param $value
     */
    private function addWhereOrSet($value){
        if($this->isSet){
            $this->setClose[] = $value;
        }
        else{
            if($this->isOr){
                $this->orWhereClose[] = $value;
            }
            else{
                $this->whereClose[] = $value;
            }
        }

    }

    /**
     * @return string
     */
    private function joinAndWhere(){
        $subWhere = [" 1=1 "];
        if($this->isOr){
            $this->isOr = false;
            $this->whereClose = array_merge($this->whereClose, [$this->joinOrWhere()]);
        }
        $this->whereClose = array_merge($subWhere,$this->whereClose);
        $where = implode( " AND ", $this->whereClose);
        $this->whereClose = [];
        return $where;
    }

    /**
     * @return string
     */
    private function joinOrWhere(){
        $where = implode( " OR ", $this->orWhereClose);
        $this->orWhereClose = [];
        return "({$where})";
    }
    private function joinSets(){
        $sets = implode( " , ", $this->setClose);
        $this->setClose = [];
        return $sets;
    }

    /**
     * @return string
     */
    private function joinInsert(){
        $valuesParams = array_map(function($el){
            return $this->valueToParams($el);
        }, (array) array_values($this->insertData));
        $values = implode(" , ",$valuesParams);
        if($this->isSequentialArray($this->insertData)){
            $keys = implode(" , ",array_keys($this->insertData));
            return " ({$keys}) VALUES ({$values}) ";
        }
        return " VALUES ({$values}) ";
    }

    private function joinRelation(){
        return implode(' ',$this->joins);
    }

    /**
     * @return string|string[]|null
     */
    public function toQueryString(){
        switch ($this->type){
            case 'SELECT':
                $relation = $this->joinRelation();
                if(strlen(trim($relation)) > 0){
                    $query =  "SELECT {$this->columns} FROM {$this->table} {$relation} WHERE  {$this->joinAndWhere()}";
                }
                else{
                    $query =  "SELECT {$this->columns} FROM {$this->table} WHERE  {$this->joinAndWhere()}";
                }
                break;
            case 'UPDATE' :
                $query = "UPDATE {$this->table} SET {$this->joinSets()}  WHERE  {$this->joinAndWhere()}";
                break;
            case 'DELETE' :
                $query = "DELETE FROM {$this->table} WHERE {$this->joinAndWhere()}";
                break;
            case 'INSERT' :
                $query = "INSERT INTO {$this->table} {$this->joinInsert()} ";
                break;
            default:
                $query = "SELECT 1+1 AS r";
                break;
        }
        return preg_replace('/\s+/', ' ',$query);
    }

    /**
     * @param $m
     */
    public function model($m){
        $this->model = $m;
    }


    /**
     * @param $columns
     * @return $this
     */
    public function select($columns){
        $this->type = "SELECT";
        if(is_array($columns)){
            $this->columns = implode(',',$columns);
        }
        else{
            $this->columns  = $columns;
        }
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function update($table){
        $this->type = "UPDATE";
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function insert($data = []){
        $this->type = "INSERT";
        $this->insertData = $data;
        return $this;
    }

    /**
     * @return $this
     */
    public function delete(){
        $this->type = "DELETE";
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function from($table){
        $this->table = $table;
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    public function into($table){
        $this->table = $table;
        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @return $this
     */
    public function set($key, $value=null){
        $this->isSet = true;
        $this->where($key, $value);
        $this->isSet = false;
        return $this;
    }

    /**
     * @param $key
     * @param $pattern
     * @return $this
     */
    public function like($key, $pattern){
        $this->where($key, $pattern, ' LIKE ');
        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @param null $operator
     * @return $this
     */
    public function where($key, $value=null, $operator=null){
        if(is_array($key)){
            foreach ($key as $k => $v) {
                $valueParam = $this->valueToParams($v);
                $this->addWhereOrSet("{$k}={$valueParam}");
            }
        }
        else{
            if($value != null){
                $valueParam = $this->valueToParams($value);
                if($operator != null){
                    $condition = " {$key}{$operator}{$valueParam} ";
                }
                else{
                    $condition = " {$key}={$valueParam} ";
                }
            }
            else{
                $condition = " {$key} ";
            }
            $this->addWhereOrSet($condition);
        }
        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @param null $operator
     * @return $this
     */
    public function orWhere($key, $value=null, $operator=null){
        $this->isOr = true;
        $this->where($key, $value,$operator);
        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @param null $operator
     * @return $this
     */
    public function andWhere($key, $value=null, $operator=null){
        $this->isOr = false;
        $orWhere = $this->joinOrWhere();
        $this->where($orWhere);
        $this->where($key, $value,$operator);
        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function whereNull($column){
        $this->where("{$column} IS NULL ");
        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function whereNotNull($column){
        $this->where("{$column} IS NOT NULL ");
        return $this;
    }

    // execution

    /**
     * @return $this
     */
    public function get(){
        $query = $this->toQueryString();
        $this->data =  $this->adapter->exec($query,$this->params, $this->model);
        return $this;
    }

    /**
     * @return bool
     */
    public function save(){
        $query = $this->toQueryString();
        $rep = $this->adapter->execSilent($query,$this->params);
        $this->type = "SELECT";
        return $rep;
    }

    /**
     * @return mixed
     */
    public function first(){
        return reset($this->data);
    }

    /**
     * @return array
     */
    public function rows(){
        return $this->data;
    }

    // RelationShip

    /**
     * @param $column1
     * @param $column2
     * @return $this
     */
    public function innerJoin($table,$column1, $column2)
    {
        $joins[] = " INNER JOIN {$table} ON {$column1}={$column2} ";
        return $this;
    }

    /**
     * @param $column1
     * @param $column2
     * @return $this
     */
    public function leftJoin($table,$column1, $column2)
    {
        $joins[] = " LEFT JOIN {$table} ON {$column1}={$column2} ";
        return $this;
    }

    /**
     * @param $column1
     * @param $column2
     * @return $this
     */
    public function rightJoin($table, $column1, $column2)
    {
        $joins[] = " RIGHT JOIN {$table} ON {$column1}={$column2} ";
        return $this;
    }

}
