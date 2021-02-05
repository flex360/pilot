<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Product;
use Flex360\Pilot\Pilot\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $productTable = (new Product())->getTable();
        $productCount = Schema::hasTable($productTable) ? DB::table($productTable)->count() : 0;
        $productCategoryTableCreated = false;
        $productCategoryTable = (new ProductCategory())->getTable();
        if (!Schema::hasTable($productCategoryTable) && $productCount == 0) {
            Schema::create($productCategoryTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->timestamps();
                $table->softDeletes();
            });
            $productCategoryTableCreated = true;
        }

        // pivot table : product_product_category
        $productCategoryPivotTable = config('pilot.table_prefix') . 'product_' .
            config('pilot.table_prefix') . 'product_category';
        // only add this if the current products table is empty
        // this protects applications with existing products tables
        if (!Schema::hasTable($productCategoryPivotTable) && $productCount == 0) {
            DB::statement('SET SESSION sql_require_primary_key=0');
            Schema::create($productCategoryPivotTable, function (Blueprint $table) use ($productCategoryTable, $productTable) {
                $table->integer('product_id')->unsigned();
                $table->foreign('product_id')->references('id')->on($productTable);

                $table->integer('product_category_id')->unsigned();
                $table->foreign('product_category_id')->references('id')->on($productCategoryTable);
                
                $table->integer('position');

                $table->primary(['product_id', 'product_category_id']);
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
