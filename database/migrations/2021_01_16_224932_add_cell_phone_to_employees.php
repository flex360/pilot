<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCellPhoneToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $employeeTable = (new Employee())->getTable();
        if (Schema::hasTable($employeeTable) && !Schema::hasColumn($employeeTable, 'cell_number')) {
            Schema::table($employeeTable, function (Blueprint $table) {
                $table->string('cell_number')->after('phone_number');
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
        // Schema::table((new Employee)->getTable(), function (Blueprint $table) {
        //     $table->dropColumn('cell_number');
        // });
    }
}
