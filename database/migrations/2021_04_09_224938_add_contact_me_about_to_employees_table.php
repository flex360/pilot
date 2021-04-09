<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Flex360\Pilot\Pilot\Employee;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactMeAboutToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $employeeTable = (new Employee())->getTable();
        if (Schema::hasTable($employeeTable) && !Schema::hasColumn($employeeTable, 'short_description')) {
            Schema::table($employeeTable, function (Blueprint $table) {
                $table->string('contact_me_about')->after('job_title');
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
