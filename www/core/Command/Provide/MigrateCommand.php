<?php

namespace Core\Command\Provide;

use Core\Command\Command;
use Core\Database\Eloquent\EloquentDB;

class MigrateCommand extends Command
{
    public $name = "migrate";

    public $description = "Run migration";

    public $migrationPath = APP_PATH . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

    /**
     * @param $args
     */
    public function handle($args)
    {
        $capsule = EloquentDB::instance();
        $pdo = $capsule->getConnection()->getPdo();
        $pdo->query('CREATE TABLE IF NOT EXISTS migrations (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, name VARCHAR(255) NOT NULL)')->execute();
        $migrations = array_map(function ($data) {
            return $data['name'];
        }, $pdo->query('SELECT name from migrations')->fetchAll(\PDO::FETCH_ASSOC), );
        $count = 0;
        foreach (glob($this->migrationPath . '*.php') as $filename) {
            $file_ext = str_replace([$this->migrationPath, '.php'], '', $filename);
            if (in_array($file_ext, $migrations)) {
                continue;
            }
            require($filename);
            $name = explode('_', $file_ext);
            $class = $name[1] . $name[0];
            $ins = new $class;
            try {
                $ins->up();
                $query = "INSERT INTO migrations (name) VALUE (?)";
                $stmt = $pdo->prepare($query);
                if ($stmt) {
                    $stmt->execute([$file_ext]);
                }
                $count++;
                consoleSucess("\nMigration of {$class} successfully");
            } catch (\Exception $e) {
                consoleError($e->getMessage() . "\n");
                $ins->down();
            }
        }
        if ($count == 0) {
            consoleInfo("\nNo migration to run");
        }
        echo "\n";
    }

}