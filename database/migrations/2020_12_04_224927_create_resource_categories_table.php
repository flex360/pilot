<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Support\Facades\Schema;
use Flex360\Pilot\Pilot\ResourceCategory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $resourceCategoryTable = (new ResourceCategory())->getTable();
        
        if (!Schema::hasTable($resourceCategoryTable)) {
            Schema::create($resourceCategoryTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // pivot table : resource_resource_category
        $resourceCategoryPivotTable = config('pilot.table_prefix') . 'resource_' .
            config('pilot.table_prefix') . 'resource_category';

        if (!Schema::hasTable($resourceCategoryPivotTable)) {
            Schema::create($resourceCategoryPivotTable, function (Blueprint $table) use ($resourceCategoryTable) {
                $table->integer('resource_id')->unsigned();
                $table->foreign('resource_id')->references('id')->on((new Resource())->getTable());

                $table->integer('resource_category_id')->unsigned();
                $table->foreign('resource_category_id', 'resource_cat_id')->references('id')
                    ->on($resourceCategoryTable);
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
        // Schema::dropIfExists((new ResourceCategory())->getTable());
        // Schema::dropIfExists(config('pilot.table_prefix') . 'resource_' . config('pilot.table_prefix') . 'resource_category');
    }
}
