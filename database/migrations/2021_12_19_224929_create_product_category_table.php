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
        Schema::create((new ProductCategory())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

         // pivot table : product_product_category
         Schema::create(config('pilot.table_prefix') . 'product_' . config('pilot.table_prefix') . 'product_category', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on((new Product())->getTable());

            $table->integer('product_category_id')->unsigned();
            $table->foreign('product_category_id')->references('id')->on((new ProductCategory())->getTable());
            $table->integer('position');
        });

        // create the Standard Example ProductCategory
        DB::table((new ProductCategory())->getTable())->insert(
            ['title' => 'Payments & Financing',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example product_product_category entry
        DB::table(config('pilot.table_prefix') . 'product_' . config('pilot.table_prefix') . 'product_category')->insert(
            ['product_id' => 1,
             'product_category_id' => 1,
             'position' => 0,
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
        Schema::dropIfExists((new ProductCategory())->getTable());
    }
}
