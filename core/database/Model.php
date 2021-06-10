<?php
namespace Core\Database;
use Core\Database\DB;
use Core\Utils\Logger;
use PDO;
use PDOException;

use function PHPSTORM_META\type;

class Model {

    protected String $table ;
    public static String $tablename;
    protected String $query = "";
    protected array $conditions = [];
    protected DB $db;
    protected String $deleted="soft_deleted=1";

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes= true;

    public function __construct()
    {
        $class = $this->endc(explode('\\',get_class($this)));
        self::$tablename = str_replace(["model","\\","models"],"",strtolower($class))."s";
        $this->db = DB::getInstance();
        // var_dump(get_class_vars(get_class($this)));
    }

    function endc($array){ 
        return end($array); 
    }

    public  function query($query){
        $this->query = $query;
        return  $this;
    }

    public  function find($ids){
        $this->conditions = [];
        if(gettype($ids)=='array'){
            $conditionsString = "id=";
            $conditionsString.=implode(' AND id=',$ids);
        }
        else{
            $conditionsString = "id=".$ids;
        }
        $this->query = "SELECT * FROM ".self::$tablename ." WHERE ".$conditionsString;
        return $this->get();
    }
    public function findBy($key,$value,$comparator="="){
        $this->conditions = [];
        $this->query = "SELECT * FROM ".self::$tablename ." WHERE ".$key.$comparator.$value;
        return $this->get();
    }
    public  function findAll(){
        $this->query = "SELECT * FROM ".self::$tablename ." ";
        return  $this;
    }

    public  function insert(array $data){
        $this->query = "INSERT INTO ".self::$tablename." SET ";
        $stringValue = [];
        foreach($data as $k => $v){
            $stringValue[] = $k."='".$v."'";
        }
        $date = date("Y-m-d H:i:s");
        $this->query .= implode(',',$stringValue).",created_at='".$date."',updated_at='".$date."'";
        return $this->execWithoutResult();
    }
    

    public  function update($id=null){
       
        if(!is_null($id)){
            $this->groupedCondition(false);
            $this->query.=" WHERE id=".$id." AND ";
        }
        else{
            $this->groupedCondition();
        }
        return $this->execWithoutResult();   
    }

    public function set($key,$value=null){
        $this->conditions = [];
        $this->query = "UPDATE ".self::$tablename;
        if(gettype($key) == "array"){
            $stringValue = [];
            foreach($key as $k => $v){
                $stringValue[] = $k."='".$v."'";
            }
        }
        else{
            $stringValue=array($key=>$value);
        }
        $this->query.=" SET ".implode(',',$stringValue);
        return $this;
    }


    public  function innerJoin(int $id = null){
        
    }

    public  function leftJoin(int $id = null){
        
    }
    public  function withDeleted(){
        
    }

    public  function where($key,$value=null,$comparator="="){
        if(gettype($key) == "array"){
            $where = array();
            foreach($key as $k => $v){
                array_push($where, $k.$comparator.'\''.$v.'\'');
            }
            $where = implode(' AND ',$where);
        }
        else{
            if(!is_null($value)){
                $where = $key.$comparator.'\''.$value.'\'';
            }
            else{
                $where = $key;
            }
        }
        array_push($this->conditions, array('AND'=>$where));
        
        return  $this;
    }
    public  function orWhere($key,$value=null,$comparator="="){
        
        if(gettype($key) == "array"){
            $where = array();
            foreach($key as $k => $v){
                array_push($where,$k.$comparator.'\''.$v.'\'');
            }
            $where = '('.implode(' AND ',$where).')';
        }
        else{
            if(!is_null($value)){
                $where = $key.$comparator.'\''.$value.'\'';
            }
            else{
                $where = $key;
            }
        }
        array_push($this->conditions, array('OR'=>$where));
        return  $this;
    }

    protected function execWithoutResult(){
        $qr = $this->db->getPDO()->prepare($this->query);
        Logger::log($this->query,"DATABASE QUERY");
        try{
            return $qr->execute();
        }
        catch(PDOException  $e){
            Logger::error($e->getMessage(),"DATABASE ERROR");
        }
        return false;
    }
    protected function execWithResult(){
        $qr = $this->db->getPDO()->prepare($this->query);
        Logger::log($this->query,"DATABASE QUERY");
        try{
            $qr->execute();
            return $qr->fetchAll(PDO::FETCH_CLASS , get_class($this));
        }
        catch(PDOException  $e){
            Logger::error($e->getMessage(),"DATABASE ERROR");
            return false;
        }
    }

    public  function get(){
        $this->groupedCondition();
        return $this->execWithResult();
    }
    public function count(){
        $this->groupedCondition();
        return count($this->execWithResult());
    }
    public function groupedCondition($withClause = true){
        $conditionsString = "";
        foreach($this->conditions as $index => $value){
            foreach($value as $k=>$v){
                $conditionsString .= $v;
                if($index + 1 != sizeof($this->conditions)){
                    $conditionsString .= " ".$k." ";
                }
            }
        }
        if($conditionsString != "") {
            if($withClause){
                $this->query.=" WHERE ".$conditionsString ;
            }
            else{
                $this->query.=" ".$conditionsString ;
            }
        };
        
    }
    public  function results(){
        
        return $this;
    }
    public  function asArray(){
        
        return $this;
    }

    

}

?>