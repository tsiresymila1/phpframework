<?php
namespace Core\Database;

class QueryBuilder {

    public  $whereClose = [];
    public $table = "";
    public $columns = "";

    public function select($columns){
        if(is_array($columns)){
            $this->columns = implode(',',$columns);
        }
        else{
            $this->columns  = $columns;
        }
    }

    public function from($table){
        $this->table = $table;
    }

    public function where($key, $value=null,$operator=null){
        if(is_array($key)){
            $this->whereClose = array_merge($this->whereClose,$key);
        }
    }

    public function orWhere(){

    }

    public function get(){

    }

    public function first(){

    }

    public function rows(){

    }

    public function whereNull(){

    }

    public function whereNotNull(){

    }
    public function innerJoin(int $id = null)
    {
    }

    public function leftJoin(int $id = null)
    {
    }
    public function rightJoin(int $id = null)
    {
    }

}
