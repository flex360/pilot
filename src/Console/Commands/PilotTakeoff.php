<?php

namespace Flex360\Pilot\Console\Commands;

use Exception;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

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
        $this->removeInitialLaravelFiles();

        // link up storage
        $this->call('storage:link');

        $this->updateDatabaseCredentials();

        $this->updateAppUrl();

        // migrate the database
        $this->call('migrate');

        $this->updateAuthConfig();

        $this->createUser();

        $this->addIgnitionVariablesToEnv();

        $this->vendorPublish();
    }

    private function removeInitialLaravelFiles()
    {
        $confirm = $this->confirm('Is this a new Laravel install?');

        if ($confirm) {
            // remove default users table migration
            try {
                unlink(base_path('database/migrations/2014_10_12_000000_create_users_table.php'));
            } catch (Exception $e) {
                $this->info('Default users table migration not found.');
            }

            // remove default welcome route
            $webRoutes = file_get_contents(base_path('routes/web.php'));
            $welcomeRouteCode = "\n\nRoute::get('/', function () {\n    return view('welcome');\n});";
            $pilotRoutes = "\n\nPilot::routesBefore();\n\n// your backend routes go here\nRoute::middleware(['web', 'pilot.global', 'auth.admin', 'backend'])\n    ->prefix('pilot')\n    ->name('admin.')\n    ->group(function () {\n        /* ---- Dynamo Routes ---- */\n\n});\n\n// your frontend routes go here\nRoute::middleware(['web', 'pilot.global'])\n    ->group(function () {\n\n});\n\nPilot::routesAfter();\n";
            $webRoutes = str_replace($welcomeRouteCode, $pilotRoutes, $webRoutes);
            file_put_contents(base_path('routes/web.php'), $webRoutes);

            // update config/database.php file to remove strict SQL setting
            $databaseConfig = file_get_contents(base_path('config/database.php'));
            $searchTerm = "'strict' => true,";
            $replaceTerm = "'strict' => false,";
            $updatedConfigFileContents = str_replace($searchTerm, $replaceTerm, $databaseConfig);
            file_put_contents(base_path('config/database.php'), $updatedConfigFileContents);
        }
    }

    private function vendorPublish()
    {
        // publish all the files needed to make Pilot work
        $this->line('Copying support files...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Flex360\Pilot\Providers\PilotServiceProvider'
        ]);
        $this->line('Support files copied!');
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

            // set config to avoid issues
            config([
                'database.connections.sqlite.database' => $database,
                'database.connections.mysql.database' => $database,
                'database.connections.mysql.username' => $username,
                'database.connections.mysql.password' => $password,
                'database.connections.pgsql.database' => $database,
                'database.connections.pgsql.username' => $username,
                'database.connections.pgsql.password' => $password,
                'database.connections.sqlsrv.database' => $database,
                'database.connections.sqlsrv.username' => $username,
                'database.connections.sqlsrv.password' => $password,
            ]);
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
