<?php

namespace Core\Database;

use Core\Utils\Logger;
use Exception;
use PDO;
use PDOException;

class DB {

        protected array $config ;
        protected PDO $db;
        private static ?DB $_instance = null;
        public String $tablename;
        
        public function __construct()
        {
            // loading database config
            if(!file_exists(DIR.'config/database.php')){
                throw new Exception('File not found');
            }
            include DIR.'config/database.php';
            $this->config = $config;
        }

        public static function getInstance() {

            if(is_null(self::$_instance)) {
            self::$_instance = new DB(); 
            } 
            return self::$_instance;
        }

        public  static function init(){
            $ins = self::getInstance();
            try{
                $ins->db = new PDO('mysql:host='. $ins->config['HOST'].';dbname='. $ins->config['DATABASE'],  $ins->config['USER'],  $ins->config['PASSWORD'], array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ));
            }
            catch  (PDOException $e) {
                    print "Erreur !: " . $e->getMessage() . "<br/>";
                    die();
            }
        }
        public static function query($query){
            $ins = self::getInstance();
            $ins->query = $query;
            Logger::log($query,"DATABASE QUERY");
            return  $ins;
        }
    
        public static function find(int $id = null){
            $ins = self::getInstance();
            return  $ins;
        }
    
        public static function select(String $tablename,$attributes = null){ 
            $ins = self::getInstance();
            $ins->query = "";
            $ins->tablename = $tablename;
            if(is_null($attributes)){
                $ins->query = "SELECT * FROM ".$ins->tablename." ";
            }
            else{
                if(gettype($attributes) == "array"){
                    $ins->query = "SELECT ".implode(',',$attributes)." FROM ".$ins->tablename;
                } 
                else{
                    $ins->query = "SELECT ".$attributes." FROM ".$ins->tablename;
                }
            }
            $ins->query .= " WHERE 1";
            return  $ins;
        }
    
        public static function insert(array $data){
            $ins = self::getInstance();
            return  $ins;
        }
    
        public static function update(int $id = null){
            $ins = self::getInstance();
            return  $ins;
        }
    
        public static function innerJoin(int $id = null){
            $ins = self::getInstance();
            return  $ins;
        }
    
        public static function leftJoin(int $id = null){
            $ins = self::getInstance();
            return  $ins;
        }
    
        public static function where(String $where){
            $ins = self::getInstance();
            return  $ins;
        }
        public static function orWhere(String $where){
            $ins = self::getInstance();
            return  $ins;
        }
    
        public static function get(){
            $ins = self::getInstance();
            $qr = $ins->db->prepare($ins->query);
            return $qr->execute();
        }
        public static function count(){
            $ins = self::getInstance();
            return $ins;
        }
        public static function results(){
            $ins = self::getInstance();
            return $ins;
        }
        public static function asArray(){
            $ins = self::getInstance();
            return $ins;
        }

    }
?>