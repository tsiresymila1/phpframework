<?php

namespace Core\Command\Provide;

use Core\Command\Command;
use Core\Database\DB;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;


class MigrateCommand extends Command
{
    public string $name = "migrate";

    public $description = "Run migration";


    /**
     * @param $args
     */
    public function handle($args): void
    {
        try {
            $capsule = DB::instance();
            // create repository
            $repository = new DatabaseMigrationRepository($capsule->getDatabaseManager(), 'migrations');
           if(!$repository->repositoryExists()){
               $repository->createRepository();
           }
            $migrator = new Migrator($repository, $capsule->getDatabaseManager(), new Filesystem());
            $migrator->run(DB::$migrationPath);
            consoleSucess( "Migration successfully !!\n");
        } catch (\Exception $e) {
            consoleError($e->getMessage() . "\n");
        }
    }

}