<?php

namespace Core\Database;

use Core\Database\DB;
use \Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Database\Migrations\Migration as EloquentMigration;

abstract class Migration extends  EloquentMigration
{

    /**
     * The name of the database connection to use.
     *
     * @var string|null
     */
    protected $connection;

    /**
     * Enables, if supported, wrapping the migration within a transaction.
     *
     * @var bool
     */
    public $withinTransaction = true;

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */

    protected SchemaBuilder $schema;

    public function __construct()
    {
        $this->schema = DB::$schema;
        $this->connection = DB::instance()->getConnection();
    }

    abstract function up();

    abstract function down();

    public function getConnection(): string
    {
        return $this->connection->getName();
    }
}