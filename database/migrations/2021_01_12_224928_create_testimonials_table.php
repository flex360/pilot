<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Testimonial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestimonialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $testimonialsTable = (new Testimonial())->getTable();
        
        if (!Schema::hasTable($testimonialsTable)) {
            Schema::create($testimonialsTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->text('quote');
                $table->string('attribution');
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
        // Schema::dropIfExists((new Testimonial())->getTable());
    }
}
