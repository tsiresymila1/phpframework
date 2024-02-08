<?php

namespace Core\Command\Provide;
use Core\Command\Command;
use DateTime;

class ServerCommand extends Command
{
    public $name =  "serve";

    public $description = "Serve with php server";

    /**
     * @param $args
     */
    public function handle($args): void
    {
        $port = sizeof($args) > 0 ? $args[0] : 4444;
        $cmd = "php -S localhost:".$port." -t ".DIR.DIRECTORY_SEPARATOR."/public 2>&1";
        $a = popen($cmd, 'r');
        consoleSucess("PHP Framework Development server on port ".$port." ..... Started");
        while($b = fgets($a, 2048)) { 
            $this->parseOutput($b);
        }
        ob_flush();
        flush();
        pclose($a);
    }

    public function parseOutput($output): void
    {
        $data = explode("]",str_replace("[","]",str_replace("]:","]",substr($output,1)),));
        if(sizeof($data) == 4){
            $date = DateTime::createFromFormat("D F j H:i:s Y", $data[0]);
            $dateString = $date->format("Y-m-d H:i:s");
            $ip = trim($data[1]);
            $statut = trim($data[2]);
            $methodAndPath = explode(" ",substr($data[3],1));
            $method = $methodAndPath[0];
            $path = str_replace("\n","",$methodAndPath[1]);
            consoleInfo("$dateString::REQUEST $method $path from  $ip with status $statut\n");
        }
    }
}
