<?php
namespace Core\Database;
use Core\Database\DB;
use Core\Utils\Logger;

use function PHPSTORM_META\type;

class Model {

    public String $table ;

    public static String $tablename;
    public String $query = "";
    protected DB $db;

    public function __construct()
    {
        $class = $this->endc(explode('\\',get_class($this)));
        self::$tablename = str_replace(["model","\\","models"],"",strtolower($class))."s";
        $this->db = DB::getInstance();
    }

    function endc($array){ 
        return end($array); 
    }

    public function query(String $query){
        $this->db::query($query);
        return $this->db;
    }

    public function select($attributes = null){
        $this->db::select(self::$tablename,$attributes);
        return $this->db;
    }

    

}

?>