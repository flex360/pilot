<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\FaqCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionToFaqCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $faqCategoryTable = (new FaqCategory())->getTable();
        if (Schema::hasTable($faqCategoryTable) && !Schema::hasColumn($faqCategoryTable, 'position')) {
            Schema::table($faqCategoryTable, function (Blueprint $table) {
                $table->integer('status')->default(30)->after('name');
                $table->integer('position')->default(0)->after('status');
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
