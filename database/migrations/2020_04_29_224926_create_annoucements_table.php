<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAnnoucementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annoucements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('headline');
            $table->string('short_description');
            $table->string('button_text');
            $table->string('button_link');
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // create the Standard exmaple Annoucement
        DB::table('users')->insert(
            ['headline' => 'Testing Alert Module',
             'short_description' => 'We\'re testing out our new alert module!',
             'button_text' => 'Did it work?',
             'button_link' => '/learn/alert-module-test',
             'status' => 1,
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
        Schema::dropIfExists('annoucements');
    }
}
