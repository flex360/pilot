<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('key', 20);
			$table->timestamps();
		});

		// add default roles
		$roles = array(
	        array('name' => 'FLEX360', 'key' => 'admin'),
	        array('name' => 'Editor', 'key' => 'editor'),
	    );

	    DB::table('roles')->insert($roles);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('roles');
	}

}
