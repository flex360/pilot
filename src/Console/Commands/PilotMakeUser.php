<?php

namespace Flex360\Pilot\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PilotMakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user in the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->anticipate('Username?', ['admin']);
        $email = $this->anticipate('Email?', ['hello@flex360.com']);
        $password = $this->ask('Password?');

        // add default users
        $users = [
            [
                'username' => $username,
                'password' => Hash::make($password),
                'role_id' => 1,
                'email' => $email
            ]
        ];

        try {
            DB::table('users')->insert($users);
        } catch (Exception $e) {
            $this->error(sprintf("Problem creating user '%s'.", $username));
        }

        $this->line(sprintf("New user '%s' created.", $username));
    }
}
