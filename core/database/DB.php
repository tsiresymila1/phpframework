<?php

namespace Core\Database;

use Core\Utils\Logger;
use Exception;
use PDO;
use PDOException;

class DB {

        protected array $config ;
        protected PDO $pdo;
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