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
        $employeeTable = (new Employee())->getTable();
        
        if (!Schema::hasTable($employeeTable)) {
            Schema::create($employeeTable, function (Blueprint $table) {
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
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists((new Employee())->getTable());
    }
}
