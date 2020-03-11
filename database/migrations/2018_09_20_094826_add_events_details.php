<?php

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
        Schema::table('events', function (Blueprint $table) {

            // Short Description
            $table->text('short_description');

            // Status
            $table->integer('status')->unsigned()->default(10);

            // Publish at Date/Time
            $table->dateTime('published_at');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['short_description', 'partner_name', 'partner_url', 'location_name', 'location_address', 'status', 'published_at']);
        });
    }
}
