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
        Schema::create((new Faq())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->text('answer');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // create the Standard Example Faq
        DB::table((new Faq())->getTable())->insert(
            ['question' => 'What is an FAQ?',
             'answer' => 'An FAQ stands for Frequently Asked Question. It is a question that our customers and friends commonly have for us.',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
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
        Schema::dropIfExists((new Faq())->getTable());
    }
}
