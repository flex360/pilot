<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Migrations\Migration;

class AddRoleIdToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->integer('role_id')->unsigned()->nullable();
            // $table->string('name');
            // $table->string('username', 100);
            // $table->timestamp('last_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
