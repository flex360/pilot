<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $servicesTable = (new Service())->getTable();
        if (!Schema::hasTable($servicesTable)) {
            Schema::create($servicesTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('icon');
                $table->string('title');
                $table->string('featured_image');
                $table->text('subservices');
                $table->text('description');
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
        // Schema::dropIfExists((new Service())->getTable());
    }
}
