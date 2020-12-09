<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Employee())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('photo');
            $table->string('first_name');
            $table->string('last_name');
            $table->dateTime('start_date');
            $table->dateTime('birth_date');
            $table->string('job_title');
            $table->string('phone_number');
            $table->string('extension');
            $table->string('email');
            $table->string('office_location');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // create the Standard Example Employee
        DB::table((new Employee())->getTable())->insert(
            ['photo' => '',
             'first_name' => 'John',
             'last_name' => 'Doe',
             'start_date' => Carbon::now()->sub(7, 'years'),
             'birth_date' => Carbon::now()->sub(25, 'years'),
             'job_title' => 'Account Executive',
             'phone_number' => '555-555-5555',
             'extension' => '317',
             'email' => 'hello@flex360.com',
             'office_location' => 'Little Rock, AR',
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
        Schema::dropIfExists((new Employee())->getTable());
    }
}
