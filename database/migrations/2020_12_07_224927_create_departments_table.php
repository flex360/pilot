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
        Schema::create((new Department())->getTable(), function (Blueprint $table) {
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

        // pivot table : employee_department
        Schema::create(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee', function (Blueprint $table) {
            $table->integer('employee_id')->unsigned();
            $table->foreign('employee_id')->references('id')->on((new Employee())->getTable());

            $table->integer('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on((new Department())->getTable());
            $table->integer('position')->default(0);
        });

        // pivot table : department_tag 
        //( this is used to relate news categories or events to this department,
        // you can use the tag related to the department to get all the posts or events on that tag )
        Schema::create(config('pilot.table_prefix') . 'department_tag', function (Blueprint $table) {
            $table->integer('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on((new Department())->getTable());

            $table->integer('tag_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        // pivot table : department_resources
        Schema::create(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'resource', function (Blueprint $table) {
            $table->integer('department_id')->unsigned();
            $table->foreign('department_id')->references('id')->on((new Department())->getTable());

            $table->integer('resource_id')->unsigned();
            $table->foreign('resource_id')->references('id')->on((new Resource())->getTable());
        });

        // create the Standard Example Department
        DB::table((new Department())->getTable())->insert(
            ['name' => 'Department Example',
             'intro_text' => 'This is text explaining some stuff about this department in the company',
             'featured_image' => '',
             'slug' => 'department-example',
             'summary' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'position' => 0,
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example employee_department entry
        DB::table(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee')->insert(
            ['employee_id' => 1,
             'department_id' => 1,
             'position' => 0,
            ]
        );

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'department_tag')->insert(
            ['department_id' => 1,
             'tag_id' => 1,
            ]
        );

        // create the example department_resources entry
        DB::table(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'resource')->insert(
            ['department_id' => 1,
             'resource_id' => 1,
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
        Schema::dropIfExists((new Department())->getTable());
    }
}
