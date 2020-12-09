<?php

use Flex360\Pilot\Pilot\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeShortDescriptionOnEventsToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Event)->getTable(), function (Blueprint $table) {
            $table->string('short_description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Event)->getTable(), function (Blueprint $table) {
            $table->text('short_description')->change();
        });
    }
}
