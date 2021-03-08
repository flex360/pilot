<?php

use Flex360\Pilot\Pilot\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeaturedImageBackgroundColorPost extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $postTable = (new Post)->getTable();
        if (Schema::hasTable($postTable) && !Schema::hasColumn($postTable, 'fi_background_color')) {
            Schema::table($postTable, function (Blueprint $table) {
                $table->string('fi_background_color')->after('vertical_featured_image');
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
        // Schema::table((new Post)->getTable(), function (Blueprint $table) {
        //     $table->dropColumn('fi_background_color');
        // });
    }

}