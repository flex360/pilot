<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table((new Event)->getTable(), function (Blueprint $table) {

            // Short Description
            $table->text('short_description');

            // Status
            $table->integer('status')->unsigned()->default(10);

            // Publish at Date/Time
            $table->dateTime('published_at');

        });

        // create the Standard Example Event
        DB::table((new Event())->getTable())->insert(
            ['title' => 'Event Example',
             'short_description' => 'This is the short description',
             'start' => Carbon::now()->sub(3, 'days'),
             'end' => Carbon::now()->add(3, 'days'),
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'gallery' => '',
             'image' => '',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
             'published_at' => Carbon::now(),
            ]
        );

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'event_tag')->insert(
            ['event_id' => 1,
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
        Schema::table((new Event)->getTable(), function (Blueprint $table) {
            $table->dropColumn(['short_description', 'partner_name', 'partner_url', 'location_name', 'location_address', 'status', 'published_at']);
        });
    }
}
