<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteIdToDefaultModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = [
            \Flex360\Pilot\Facades\Post::getTable(),
            \Flex360\Pilot\Facades\Event::getTable(),
            \Flex360\Pilot\Facades\Annoucement::getTable(),
            \Flex360\Pilot\Facades\Resource::getTable(),
            \Flex360\Pilot\Facades\ResourceCategory::getTable(),
            \Flex360\Pilot\Facades\Employee::getTable(),
            \Flex360\Pilot\Facades\Department::getTable(),
            \Flex360\Pilot\Facades\Testimonial::getTable(),
            \Flex360\Pilot\Facades\Faq::getTable(),
            \Flex360\Pilot\Facades\FaqCategory::getTable(),
            \Flex360\Pilot\Facades\Product::getTable(),
            \Flex360\Pilot\Facades\ProductCategory::getTable(),
            \Flex360\Pilot\Facades\Project::getTable(),
            \Flex360\Pilot\Facades\ProjectCategory::getTable(),
            \Flex360\Pilot\Facades\Service::getTable(),
            \Flex360\Pilot\Facades\ServiceCategory::getTable(),
            \Flex360\Pilot\Facades\Tag::getTable()
        ];
        
        foreach ($tables as $moduleTable) {
            Schema::table($moduleTable, function (Blueprint $table) {
                $table->unsignedInteger('site_id')->nullable()->after('id')->default(1);
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
        //
    }
}
