<?php

use Core\Database\Eloquent\EloquentMigration;
use Illuminate\Database\Schema\Blueprint;

class UserMigration1691402634 extends EloquentMigration
{
    public function up()
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
    public function down()
    {

    }
}