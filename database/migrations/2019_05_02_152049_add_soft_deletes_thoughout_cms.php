<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeletesThoughoutCms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::table('pages', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('events', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('posts', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('roles', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('settings', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('sites', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('tags', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });

         Schema::table('users', function (Blueprint $table) {
             $table->softDeletes()->after('updated_at');
         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::table('pages', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('events', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('posts', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('roles', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('settings', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('sites', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('tags', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });

         Schema::table('users', function (Blueprint $table) {
             $table->dropColumn('deleted_at');
         });
     }
}
