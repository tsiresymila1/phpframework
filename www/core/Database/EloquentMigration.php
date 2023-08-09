<?php
namespace Core\Database\Eloquent;

use Core\Database\DB;
use \Illuminate\Database\Schema\Builder as SchemaBuilder;
abstract class EloquentMigration
{

    protected SchemaBuilder $schema;
    public function __construct()
    {
        $this->schema = DB::$schema;
    }

    abstract function up();
    abstract function down();
}