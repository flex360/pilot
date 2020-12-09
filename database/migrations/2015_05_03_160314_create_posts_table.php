<?php

use Flex360\Pilot\Pilot\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Post)->getTable(), function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title', 255);
            $table->tinyInteger('status')->unsigned()->nullable();
            $table->text('summary');
            $table->mediumText('body');
            $table->string('image', 255);
            $table->text('gallery');
            $table->dateTime('published_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop((new Post)->getTable());
    }

}