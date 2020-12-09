<?php

use Flex360\Pilot\Pilot\Event;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Event)->getTable(), function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title', 255);
            $table->dateTime('start');
            $table->dateTime('end');
            $table->text('body');
            $table->text('gallery');
            $table->string('image', 255);
            $table->timestamps();
        });

        // pivot table : event_tag
        Schema::create(config('pilot.table_prefix') . 'event_tag', function(Blueprint $table)
        {
            $table->integer('event_id')->unsigned()->nullable();
            $table->foreign('event_id')->references('id')->on((new Event)->getTable());

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
        Schema::drop(config('pilot.table_prefix') . 'event_tag');
        Schema::drop((new Event)->getTable());
    }

}