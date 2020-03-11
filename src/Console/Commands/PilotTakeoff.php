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
        // $this->vendorPublish();

        // // link up storage
        // $this->call('storage:link');

        // $this->updateAppUrl();

        $this->updateDatabaseCredentials();

        // // migrate the database
        // $this->call('migrate');

        // $this->updateAuthConfig();

        // $this->createUser();

        // $this->addIgnitionVariablesToEnv();
    }

    private function vendorPublish()
    {
        // publish all the files needed to make Pilot work
        $this->call('vendor:publish', [
            '--provider' => 'Flex360\Pilot\Providers\PilotServiceProvider'
        ]);
    }

    private function updateAppUrl()
    {
        // update APP_URL in .env
        $appUrl = 'APP_URL=' . $this->ask('App url (ie http://pilot.test)?');
        $envContent = file_get_contents(base_path('.env'));
        file_put_contents(base_path('.env'), str_replace('APP_URL=http://localhost', $appUrl, $envContent));
    }

    private function updateDatabaseCredentials()
    {
        $confirm = $this->confirm('Update database credenitals in .env?');
        
        if ($confirm) {
            $database = $this->ask('Database name?');
            $username = $this->ask('Database username?');
            $password = $this->ask('Database password?');

            $envContent = file_get_contents(base_path('.env'));
            $envContent = str_replace('DB_DATABASE=laravel', 'DB_DATABASE=' . $database, $envContent);
            $envContent = str_replace('DB_USERNAME=root', 'DB_USERNAME=' . $username, $envContent);
            $envContent = str_replace('DB_PASSWORD=', 'DB_PASSWORD=' . $password, $envContent);
            file_put_contents(base_path('.env'), $envContent);
        }
    }

    private function updateAuthConfig()
    {
        // update config/auth.php
        $authContent = file_get_contents(base_path('config/auth.php'));
        file_put_contents(base_path('config/auth.php'), str_replace('App\User::class', 'PilotUser::class', $authContent));
    }

    private function createUser()
    {
        // create a new user
        $confirm = $this->confirm('Would you like to create a user?');
        if ($confirm) {
            $this->call('pilot:user');
        }
    }

    private function addIgnitionVariablesToEnv()
    {
        // add Ignition variables to .env
        $confirm = $this->confirm('Would you like to provide Ignition paths?');
        if ($confirm) {
            $remoteSitesPath = $this->anticipate('Remote sites path?', ['/home/vagrant/code']);
            $localSitesPath = $this->anticipate('Local sites path?', ['/Users/{user}/Code']);
            $envContent = file_get_contents(base_path('.env'));
            $envContent = $envContent
                . "\r\n\r\nIGNITION_REMOTE_SITES_PATH=$remoteSitesPath\r\nIGNITION_LOCAL_SITES_PATH=$localSitesPath\r\n";
            file_put_contents(base_path('.env'), $envContent);
        }
    }
}
