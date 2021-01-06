<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Post)->getTable(), function (Blueprint $table) {
            $table->string('slug');
        });

        // create the Standard Post Example
        DB::table((new Post())->getTable())->insert(
            ['title' => 'Post Example',
             'summary' => 'This is the post summary',
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'horizontal_featured_image' => '',
             'vertical_featured_image' => '',
             'gallery' => '',
             'external_link' => '',
             'status' => 10,
             'published_on' => Carbon::now(),
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),             
            ]
        );

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'post_tag')->insert(
            ['post_id' => 1,
             'tag_id' => 1,
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
        Schema::table((new Post)->getTable(), function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
