<?php
    namespace Core\Utils;
    use DateTime;

    class Logger {

        public static function log($log,$flag="LOG"){
            $date = new DateTime();
            $datestring = $date->format('Y-m-d H:i:s');
            file_put_contents(DIR."storage/server.log",$datestring."::".$flag.":: ".$log.PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        public static function error($log,$flag="ERROR"){
            $date = new DateTime();
            $datestring = $date->format('Y-m-d H:i:s');
            file_put_contents(DIR."storage/server.log",$datestring."::".$flag.":: ".$log.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        public static function infos($log,$flag="INFOD"){
            $date = new DateTime();
            $datestring = $date->format('Y-m-d H:i:s');
            file_put_contents(DIR."storage/server.log",$datestring."::".$flag.":: ".$log.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        public static function warning($log,$flag="WARNING"){
            $date = new DateTime();
            $datestring = $date->format('Y-m-d H:i:s');
            file_put_contents(DIR."storage/server.log",$datestring."::".$flag.":: ".$log.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

?>