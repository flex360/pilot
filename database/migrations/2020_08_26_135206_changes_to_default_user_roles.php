<?php

use Flex360\Pilot\Pilot\Role;
use Flex360\Pilot\Pilot\User;
use Illuminate\Database\Migrations\Migration;

class ChangesToDefaultUserRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::where('key', 'admin')->update([
            'name' => 'Admin',
        ]);

        $role = Role::create([
            'name' => 'Super Admin',
            'key' => 'super',
        ]);

        User::where('username', 'admin')->update([
            'role_id' => $role->id,
        ]);
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
