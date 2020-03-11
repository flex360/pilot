<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('meta_title');
			$table->text('meta_description');
			$table->string('slug');
			$table->text('body');
			$table->timestamps();
			$table->integer('site_id')->unsigned()->nullable();

	        $table->string('path', 255)->nullable();
	        $table->integer('parent_id')->unsigned()->nullable();
	        $table->integer('level')->default(0);
	        $table->index(array('path', 'parent_id', 'level'));
	        $table->foreign('parent_id')->references('id')->on('pages')->onDelete('CASCADE');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
	}

}
