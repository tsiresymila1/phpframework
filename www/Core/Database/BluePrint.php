<?php

class Column {
    public $name;
    public $type;
    public $size ;
    public $isPrimary;
    public $isAutoIncrement;
    public $isnull = "NOT NULL";
    public $defaultValue;
    public $index;
    public $commentaire;

    public function default($value){
        $this->default = $value;
    }

    public function nullable($value){
        $this->isnull = "NULL";
    }

    public function toSql(){
        return " `$this->name` $this->type($this->size) $this->nullable $this->isPrimary $this->isAutoIncrement";
    }
    
}

class BluePrint {
    private static $_instance;

    public static $engine = "InnoDB";

    public function __construct()
    {
        
    }

    public static function instance(){
        if(is_null(self::$_instance)){
            return new BluePrint();
        }else{
            return self::$_instance;
        }
    }

    public function bigIncrements($column){
        $c = new Column();
        $c->name = $column;
        $c->isPrimary = true;
        $c->isAutoIncrement = true;
        $c->isnull = false;
        $c->size = 11;
        return $c;
    }
    public function string($column,$value){

    }
    
}

