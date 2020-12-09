<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Resource())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('short_description');
            $table->text('link');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // create the Standard Example Resource
        DB::table((new Resource())->getTable())->insert(
            ['title' => 'Cute puppies',
             'short_description' => 'This is a test resource that links to cute puppies',
             'link' => 'https://www.google.com/search?ei=LBTMX6zWBpKxtQbhi5OYCw&q=cute+puppies&oq=cute+puppies&gs_lcp=CgZwc3ktYWIQAzIFCAAQsQMyAggAMgIIADICCAAyBQgAEMkDMgIIADICCAAyAggAMgIIADICCAA6BAgAEEc6CAguEJECEJMCOgsILhDHARCjAhCRAjoICAAQsQMQgwE6CAguEMcBEKMCOg4ILhCxAxCDARDHARCjAjoFCAAQkQI6CwguEMcBEK8BEJECOhEILhDHARCvARDJAxCRAhCTAjoFCC4QsQM6CAgAELEDEMkDOgcIABCxAxAKOgQIABAKOgcIABDJAxAKUJwlWKUzYNwzaAFwAXgAgAGCAYgBuAiSAQM3LjSYAQCgAQGqAQdnd3Mtd2l6yAEIwAEB&sclient=psy-ab&ved=0ahUKEwisnLj2-7ftAhWSWM0KHeHFBLMQ4dUDCA0&uact=5',
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
        Schema::dropIfExists((new Resource())->getTable());
    }
}
