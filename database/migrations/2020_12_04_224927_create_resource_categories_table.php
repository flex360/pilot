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
        Schema::create((new ResourceCategory())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // pivot table : resource_resource_category
        Schema::create(config('pilot.table_prefix') . 'resource_' . config('pilot.table_prefix') . 'resource_category', function (Blueprint $table) {
            $table->integer('resource_id')->unsigned();
            $table->foreign('resource_id')->references('id')->on((new Resource())->getTable());

            $table->integer('resource_category_id')->unsigned();
            $table->foreign('resource_category_id', 'resource_cat_id')->references('id')->on((new ResourceCategory())->getTable());
        });

        // create the Standard Example Resource Category
        DB::table((new ResourceCategory())->getTable())->insert(
            ['name' => 'Example Category',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example resource_resource_category entry
        DB::table(config('pilot.table_prefix') . 'resource_' . config('pilot.table_prefix') . 'resource_category')->insert(
            ['resource_id' => 1,
             'resource_category_id' => 1,
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
        Schema::dropIfExists((new ResourceCategory())->getTable());
        Schema::dropIfExists(config('pilot.table_prefix') . 'resource_' . config('pilot.table_prefix') . 'resource_category');
    }
}
