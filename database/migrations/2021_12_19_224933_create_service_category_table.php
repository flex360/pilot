<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Project;
use Flex360\Pilot\Pilot\Service;
use Flex360\Pilot\Pilot\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $serviceTable = (new Service())->getTable();
        $serviceCount = Schema::hasTable($serviceTable) ? DB::table($serviceTable)->count() : 0;
        $serviceCategoryTableCreated = false;
        $serviceCategoryTable = (new ServiceCategory())->getTable();
        if (!Schema::hasTable($serviceCategoryTable) && $serviceCount == 0) {
            Schema::create($serviceCategoryTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('featured_image');
                $table->integer('position')->default(0);
                $table->integer('status');
                $table->timestamps();
                $table->softDeletes();
            });
            $serviceCategoryTableCreated = true;
        }

        // pivot table : service_service_category
        $serviceCategoryPivotTable = config('pilot.table_prefix') . 'service_' .
            config('pilot.table_prefix') . 'service_category';
        // only add this if the current services table is empty
        // this protects applications with existing services tables
        if (!Schema::hasTable($serviceCategoryPivotTable) && $serviceCount == 0) {
            if (count(DB::select("SHOW VARIABLES LIKE 'sql_require_primary_key'")) > 0) {
                DB::statement('SET SESSION sql_require_primary_key=0');
            }
            Schema::create($serviceCategoryPivotTable, function (Blueprint $table) use ($serviceCategoryTable, $serviceTable) {
                $table->integer('service_id')->unsigned();
                $table->foreign('service_id')->references('id')->on($serviceTable);

                $table->integer('service_category_id')->unsigned();
                $table->foreign('service_category_id')->references('id')->on($serviceCategoryTable);
                
                $table->integer('position');

                $table->primary(['service_id', 'service_category_id'], 'pilot_service_id_service_category_id_primary');
            });
        }

        // pivot table : project_service
        $projectTable = (new Project())->getTable();
        $projectServicePivotTable = config('pilot.table_prefix') . 'project_' .
            config('pilot.table_prefix') . 'service';
        // only add this if the current services table is empty
        // this protects applications with existing services tables
        if (!Schema::hasTable($projectServicePivotTable) && $serviceCount == 0) {
            if (count(DB::select("SHOW VARIABLES LIKE 'sql_require_primary_key'")) > 0) {
                DB::statement('SET SESSION sql_require_primary_key=0');
            }
            Schema::create($projectServicePivotTable, function (Blueprint $table) use ($serviceTable, $projectTable) {
                $table->integer('project_id')->unsigned();
                $table->foreign('project_id')->references('id')->on($projectTable);

                $table->integer('service_id')->unsigned();
                $table->foreign('service_id')->references('id')->on($serviceTable);
                
                $table->integer('position');

                $table->primary(['project_id', 'service_id'], 'pilot_project_id_service_id_primary');
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
