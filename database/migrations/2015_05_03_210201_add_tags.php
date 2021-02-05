<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTags extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 255);
            $table->timestamps();
        });

        Schema::create(config('pilot.table_prefix') . 'post_tag', function(Blueprint $table)
        {
            $table->integer('post_id')->unsigned()->nullable();
            $table->foreign('post_id')->references('id')->on((new Post)->getTable());

            $table->integer('tag_id')->unsigned()->nullable();
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('pilot.table_prefix') . 'post_tag');
        Schema::drop('tags');
    }

}