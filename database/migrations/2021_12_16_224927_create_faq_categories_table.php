<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Faq;
use Flex360\Pilot\Pilot\FaqCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new FaqCategory())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

         // pivot table : faq_faq_category
         Schema::create(config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category', function (Blueprint $table) {
            $table->integer('faq_id')->unsigned();
            $table->foreign('faq_id')->references('id')->on((new Faq())->getTable());

            $table->integer('faq_category_id')->unsigned();
            $table->foreign('faq_category_id')->references('id')->on((new FaqCategory())->getTable());
            $table->integer('position');
        });

        // create the Standard Example FaqCategory
        DB::table((new FaqCategory())->getTable())->insert(
            ['name' => 'Payments & Financing',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example faq_faq_category entry
        DB::table(config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category')->insert(
            ['faq_id' => 1,
             'faq_category_id' => 1,
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
        Schema::dropIfExists((new FaqCategory())->getTable());
    }
}
