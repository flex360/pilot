<?php

namespace Flex360\Pilot\Console\Commands;

use Illuminate\Console\Command;

class PilotTakeoff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:takeoff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do all the initial stuff to get started with Pilot';

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
        // publish all the files needed to make Pilot work
        $this->call('vendor:publish', [
            '--provider' => 'Flex360\Pilot\Providers\PilotServiceProvider'
        ]);

        // link up storage
        $this->call('storage:link');

        // migrate the database
        $this->call('migrate');

        // update config/auth.php
        $authContent = file_get_contents(base_path('config/auth.php'));
        file_put_contents(base_path('config/auth.php'), str_replace('App\User::class', 'PilotUser::class', $authContent));

        // create a new user
        $this->call('pilot:user');

        // add Ignition variables to .env
        $remoteSitesPath = $this->anticipate('Remote sites path?', ['/home/vagrant/code']);
        $localSitesPath = $this->anticipate('Local sites path?', ['/Users/{user}/Code']);
        $envContent = file_get_contents(base_path('.env'));
        $envContent = $envContent
            . "\r\n\r\nIGNITION_REMOTE_SITES_PATH=$remoteSitesPath\r\nIGNITION_LOCAL_SITES_PATH=$localSitesPath\r\n";
        file_put_contents(base_path('.env'), $envContent);
    }
}
