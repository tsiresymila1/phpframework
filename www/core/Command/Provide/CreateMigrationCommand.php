<?php

namespace Core\Command\Provide;

use Core\Command\Command;
use Core\Database\DB;

class CreateMigrationCommand extends Command
{
    public string $name = "migration:create";

    public $description = "Create migration file";


    /**
     * @param $args
     */
    public function handle($args)
    {
        if (sizeof($args) > 0) {
            $migname = $args[0];
            consoleSucess(json_encode($args));
            $re = '/(?<=[a-z])(?=[A-Z])/x';
            $a = preg_split($re, $migname);
            $timestamp = (new \DateTime())->format("Y_m_d_his");
            $migfilename = strtolower($timestamp . '_' . join('_',$a));
            $tableIndice = array_search('--table', $args);
            if (sizeof($args) >= $tableIndice + 2) {
                $tablename = $args[$tableIndice + 1];
                $content = $this->getContent($migname, $tablename);
            } else {
                $content = $this->getContent($migname, null);
            }
            file_put_contents(DB::$migrationPath . $migfilename . ".php", $content);
            consoleSucess(" Migration ".$migname . " created successfully ");

        } else {
            consoleError("\nMigration name not provided");
        }
        echo "\n";
    }
    public function getContent($name, $tablename): string
    {
        $cap = ucfirst($name);

        $up = $tablename ? <<<PHP
        \$this->schema->create("{$tablename}", function (Blueprint \$table) {});
        PHP : "";

        $down = $tablename ? <<<PHP
        \$this->schema->drop("{$tablename}");
        PHP : "";


        return <<<PHP
        <?php

        use Core\Database\Migration;
        use Illuminate\Database\Schema\Blueprint;

        class {$cap} extends Migration
        {
            public function up()
            {
                {$up}
            }
            public function down()
            {
                {$down}
            }
        }
        PHP;
    }
}