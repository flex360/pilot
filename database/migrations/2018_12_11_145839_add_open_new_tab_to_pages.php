<?php

use Flex360\Pilot\Pilot\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenNewTabToPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Page)->getTable(), function (Blueprint $table) {

            if (! Schema::hasColumn((new Page)->getTable(), 'open_in_new_tab')) {
                $table->boolean('open_in_new_tab')->default(0);
            }

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
