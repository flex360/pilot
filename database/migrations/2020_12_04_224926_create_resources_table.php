<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable((new Resource())->getTable())) {
            Schema::create((new Resource())->getTable(), function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('short_description');
                $table->text('link');
                $table->integer('status');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists((new Resource())->getTable());
    }
}
