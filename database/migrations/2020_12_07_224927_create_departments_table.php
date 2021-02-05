<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Employee;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Support\Facades\DB;
use Flex360\Pilot\Pilot\Department;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $departmentTable = (new Department())->getTable();

        if (!Schema::hasTable($departmentTable)) {
            Schema::create($departmentTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('intro_text');
                $table->text('featured_image');
                $table->string('slug');
                $table->text('summary');
                $table->integer('position')->default(0);
                $table->integer('status');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        DB::statement('SET SESSION sql_require_primary_key=0');

        // pivot table : employee_department
        $employeeDepartmentTable = config('pilot.table_prefix') . 'department_' .
            config('pilot.table_prefix') . 'employee';
        
        if (!Schema::hasTable($employeeDepartmentTable)) {
            Schema::create($employeeDepartmentTable, function (Blueprint $table) use ($departmentTable) {
                $table->integer('employee_id')->unsigned();
                $table->foreign('employee_id')->references('id')->on((new Employee())->getTable());

                $table->integer('department_id')->unsigned();
                $table->foreign('department_id')->references('id')->on($departmentTable);
                $table->integer('position')->default(0);

                $table->primary(['employee_id', 'department_id']);
            });
        }

        // pivot table : department_tag 
        //( this is used to relate news categories or events to this department,
        // you can use the tag related to the department to get all the posts or events on that tag )
        $departmentTagTable = config('pilot.table_prefix') . 'department_tag';
        if (!Schema::hasTable($departmentTagTable)) {
            Schema::create($departmentTagTable, function (Blueprint $table) use ($departmentTable) {
                $table->integer('department_id')->unsigned();
                $table->foreign('department_id')->references('id')->on($departmentTable);

                $table->integer('tag_id')->unsigned();
                $table->foreign('tag_id')->references('id')->on('tags');

                $table->primary(['department_id', 'tag_id']);
            });
        }

        // pivot table : department_resources
        $departmentResourceTable = config('pilot.table_prefix') . 'department_' .
            config('pilot.table_prefix') . 'resource';

        if (!Schema::hasTable($departmentResourceTable)) {
            Schema::create($departmentResourceTable, function (Blueprint $table) use ($departmentTable) {
                $table->integer('department_id')->unsigned();
                $table->foreign('department_id')->references('id')->on($departmentTable);

                $table->integer('resource_id')->unsigned();
                $table->foreign('resource_id')->references('id')->on((new Resource())->getTable());

                $table->primary(['department_id', 'resource_id']);
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
        // Schema::dropIfExists((new Department())->getTable());
    }
}
