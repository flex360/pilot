<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Post)->getTable(), function (Blueprint $table) {
            $table->string('slug');
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
            $table->dropColumn('slug');
        });
    }
}
