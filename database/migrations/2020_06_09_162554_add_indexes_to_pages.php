<?php

use Flex360\Pilot\Pilot\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Page)->getTable(), function (Blueprint $table) {
            $table->index('path');
            $table->index('site_id');
            $table->index('deleted_at');
            $table->index(['path', 'site_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Page)->getTable(), function (Blueprint $table) {
            //
        });
    }
}
