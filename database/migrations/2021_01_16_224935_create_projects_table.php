<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projectsTable = (new Project())->getTable();
        if (!Schema::hasTable($projectsTable)) {
            Schema::create($projectsTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('featured_image');
                $table->string('fi_background_color');
                $table->text('summary');
                $table->string('location');
                $table->dateTime('completion_date')->nullable();
                $table->string('gallery');
                $table->boolean('featured');
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
        // Schema::dropIfExists((new Project())->getTable());
    }
}
