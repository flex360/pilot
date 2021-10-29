<?php

use Flex360\Pilot\Facades\Employee as EmployeeFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeEmployeesStartDateAndBirthDateNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $employeeTable = EmployeeFacade::getTable();
        
        Schema::table($employeeTable, function (Blueprint $table) {
            $table->dateTime('start_date')->nullable()->change();
            $table->dateTime('birth_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
}
