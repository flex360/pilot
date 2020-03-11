<?php

use Flex360\Pilot\Pilot\PageType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_types', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name', 100);
            $table->text('slug', 100);
            $table->integer('page_id')->nullable();
            $table->timestamps();
        });

        PageType::create(['name' => 'Page', 'slug' => 'page']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_types');
    }
}
