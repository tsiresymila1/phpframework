<?php

use Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class UserMigrationCreate  extends Migration
{
    public function up(): void
    {
        $this->schema->create("users", function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('userimage')->nullable();
            $table->string('api_key')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        $this->schema->drop("users");
    }
}