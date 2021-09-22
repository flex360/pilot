<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Resource;
use Flex360\Pilot\Pilot\ResourceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToResourcesAndCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $resourceTable = (new Resource())->getTable();
        if (Schema::hasTable($resourceTable) && !Schema::hasColumn($resourceTable, 'position')) {
            Schema::table($resourceTable, function (Blueprint $table) {
                $table->integer('position')->default(0)->after('status');
            });
        }

        $resourceCategoryTable = (new ResourceCategory())->getTable();
        if (Schema::hasTable($resourceCategoryTable) && !Schema::hasColumn($resourceCategoryTable, 'position')) {
            Schema::table($resourceCategoryTable, function (Blueprint $table) {
                $table->integer('status')->default(30)->after('name');
                $table->integer('position')->default(0)->after('status');
            });
        }


        $resourcePivotTable = config('pilot.table_prefix') . 'resource_' .
                                 config('pilot.table_prefix') . 'resource_category';
        if (Schema::hasTable($resourcePivotTable) && !Schema::hasColumn($resourcePivotTable, 'position')) {
            Schema::table($resourcePivotTable, function (Blueprint $table) {
                $table->integer('position')->default(0);
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
