<?php

use Flex360\Pilot\Pilot\Page;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBreadcrumbToPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Page)->getTable(), function (Blueprint $table) {
            $table->string('breadcrumb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table((new Page)->getTable(), function (Blueprint $table) {
            $table->dropColumn('breadcrumb');
        });
    }
}
