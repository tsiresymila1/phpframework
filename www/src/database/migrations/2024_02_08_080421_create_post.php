<?php

use Core\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePost extends Migration
{
    public function up()
    {
        $this->schema->create("posts", function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('description');
            $table->timestamps();
        });
    }
    public function down()
    {
        $this->schema->drop("posts");
    }
}