<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Faq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $faqsTable = (new Faq())->getTable();
        
        if (!Schema::hasTable($faqsTable)) {
            Schema::create($faqsTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('question');
                $table->text('answer');
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
        // Schema::dropIfExists((new Faq())->getTable());
    }
}
