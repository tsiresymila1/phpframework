<?php

namespace Core\Database;
use Exception;
use PDO;
use PDOException;

class DB {

        protected  $config ;
        protected  $pdo;
        private static  $_instance = null;
        public  $tablename;
        
        public function __construct()
        {
            // loading database config
            if(!file_exists(APP_PATH.'config/database.php')){
                throw new Exception('File not found');
            }
            require APP_PATH.'config/database.php';
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
                $ins->pdo = new PDO('mysql:host='. $ins->config['HOST'].';dbname='. $ins->config['DATABASE'],  $ins->config['USER'],  $ins->config['PASSWORD'], array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ));
            }
            catch  (PDOException $e) {
                    echo "Erreur !: " . $e->getMessage() . "<br/>";
                    die();
            }
        }
        public function getPDO(){
            return $this->pdo;
        }
    }
?>