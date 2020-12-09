<?php

use Flex360\Pilot\Pilot\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HorizonalVerticalFeaturedImagePosts2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Post)->getTable(), function (Blueprint $table) {
            $table->string('vertical_featured_image')->after('horizontal_featured_image');
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
            $table->dropColumn('vertical_featured_image');
        });
    }
}
