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
        $faqCategoryTable = (new FaqCategory())->getTable();
        if (!Schema::hasTable($faqCategoryTable)) {
            Schema::create($faqCategoryTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // pivot table : faq_faq_category
        $faqTable = (new Faq())->getTable();
        $faqCategoryPivotTable = config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category';
        if (!Schema::hasTable($faqCategoryPivotTable)) {
            DB::statement('SET SESSION sql_require_primary_key=0');
            Schema::create($faqCategoryPivotTable, function (Blueprint $table) use ($faqCategoryTable, $faqTable) {
                $table->integer('faq_id')->unsigned();
                $table->foreign('faq_id')->references('id')->on($faqTable);

                $table->integer('faq_category_id')->unsigned();
                $table->foreign('faq_category_id')->references('id')->on($faqCategoryTable);
                $table->integer('position');

                $table->primary(['faq_id', 'faq_category_id'], 'pilot_faq_id_faq_category_id_primary');
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
        // Schema::dropIfExists((new FaqCategory())->getTable());
    }
}
