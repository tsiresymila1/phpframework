<?php

namespace Core\Command\Provide;

use Core\Command\Command;

class CreateMigrationCommand extends Command
{
    public $name = "migration:create";

    public $description = "Create migration file ";

    public $migrationPath = APP_PATH . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

    /**
     * @param $args
     */
    public function handle($args)
    {
        if (sizeof($args) > 0) {
            $migname = $args[0];
            $sub = substr(strtolower($migname), -9, 9);
            if ($sub === 'migration') {
                $prefix = strtolower(substr_replace($migname, '', -9, 9));
            } else {
                $prefix = strtolower($migname);
            }
            $timestamp = (new \DateTime())->getTimestamp();
            $migname = ucfirst($prefix) . 'Migration' . $timestamp . '';
            $migfilename = $timestamp . '_' . ucfirst($prefix) . 'Migration';
            $tableIndice = array_search('--table', $args);
            if (sizeof($args) >= $tableIndice + 2) {
                $tablename = $args[$tableIndice + 1];
                $content = $this->getContent($migname, $tablename);
            } else {
                $content = $this->getContent($migname, null);
            }
            file_put_contents($this->migrationPath . $migfilename . ".php", $content);
            consoleSucess($migname . " created successfully ");

        } else {
            consoleError("\nMigration name not provided");
        }
        echo "\n";
    }
    public function getContent($name, $tablename)
    {
        $cap = ucfirst($name);

        $up = $tablename ? <<<PHP
        \$this->schema->create("{$tablename}", function (Blueprint \$table) {
                    
                });
        PHP : "";

        return <<<PHP
        <?php

        use Core\Database\Eloquent\EloquentMigration;
        use Illuminate\Database\Schema\Blueprint;

        class {$cap} extends EloquentMigration
        {
            public function up()
            {
                {$up}
            }
            public function down()
            {
     
            }
        }
        PHP;
    }
}