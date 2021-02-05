<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Annoucement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnoucementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Annoucement())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('headline');
            $table->string('short_description');
            $table->string('button_text');
            $table->string('button_link');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Annoucement())->getTable());
    }
}
