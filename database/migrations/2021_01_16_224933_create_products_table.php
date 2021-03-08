<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $productsTable = (new Product())->getTable();
        if (!Schema::hasTable($productsTable)) {
            Schema::create($productsTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('price');
                $table->string('short_description');
                $table->text('full_description');
                $table->integer('status');
                $table->timestamps();
                $table->softDeletes();
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
        // Schema::dropIfExists((new Product())->getTable());
    }
}
