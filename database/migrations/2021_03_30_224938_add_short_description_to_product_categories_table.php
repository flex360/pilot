<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortDescriptionToProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $productCategoryTable = (new ProductCategory())->getTable();
        if (Schema::hasTable($productCategoryTable) && !Schema::hasColumn($productCategoryTable, 'short_description')) {
            Schema::table($productCategoryTable, function (Blueprint $table) {
                $table->string('short_description')->after('title');
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
