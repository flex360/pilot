<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Project;
use Flex360\Pilot\Pilot\ProjectCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projectTable = (new Project())->getTable();
        $projectCount = Schema::hasTable($projectTable) ? DB::table($projectTable)->count() : 0;
        $projectCategoryTableCreated = false;
        $projectCategoryTable = (new ProjectCategory())->getTable();
        if (!Schema::hasTable($projectCategoryTable) && $projectCount == 0) {
            Schema::create($projectCategoryTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('featured_image');
                $table->integer('position')->default(0);
                $table->integer('status');
                $table->timestamps();
                $table->softDeletes();
            });
            $projectCategoryTableCreated = true;
        }

        // pivot table : project_project_category
        $projectCategoryPivotTable = config('pilot.table_prefix') . 'project_' .
            config('pilot.table_prefix') . 'project_category';
        // only add this if the current projects table is empty
        // this protects applications with existing projects tables
        if (!Schema::hasTable($projectCategoryPivotTable) && $projectCount == 0) {
            if (count(DB::select("SHOW VARIABLES LIKE 'sql_require_primary_key'")) > 0) {
                DB::statement('SET SESSION sql_require_primary_key=0');
            }
            Schema::create($projectCategoryPivotTable, function (Blueprint $table) use ($projectCategoryTable, $projectTable) {
                $table->integer('project_id')->unsigned();
                $table->foreign('project_id')->references('id')->on($projectTable);

                $table->integer('project_category_id')->unsigned();
                $table->foreign('project_category_id')->references('id')->on($projectCategoryTable);
                
                $table->integer('position');

                $table->primary(['project_id', 'project_category_id'], 'pilot_project_id_project_category_id_primary');
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
        // Schema::dropIfExists((new ProductCategory())->getTable());
    }
}
