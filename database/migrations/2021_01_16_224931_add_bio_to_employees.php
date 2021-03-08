<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBioToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $employeeTable = (new Employee())->getTable();
        if (Schema::hasTable($employeeTable) && !Schema::hasColumn($employeeTable, 'bio')) {
            Schema::table($employeeTable, function (Blueprint $table) {
                $table->text('bio')->after('office_location');
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
        //     $table->dropColumn('bio');
        // });
    }
}
