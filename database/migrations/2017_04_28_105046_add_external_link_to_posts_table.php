<?php

use Flex360\Pilot\Pilot\Post;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExternalLinkToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Post)->getTable(), function (Blueprint $table) {
            $table->string('external_link');
            $table->string('author');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Post)->getTable(), function (Blueprint $table) {
            $table->dropColumn(['external_link', 'author']);
        });
    }
}
