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
        Schema::create((new Testimonial())->getTable(), function (Blueprint $table) {
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

        // create the Standard Example Testimonial
        DB::table((new Testimonial())->getTable())->insert(
            ['name' => 'Jane Smith',
             'city' => 'Paris',
             'state' => 'Arkansas',
             'country' => '',
             'quote' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Testimonial())->getTable());
    }
}
