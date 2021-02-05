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
     * The console command description.
     *
     * @var string
     */
    public $appUrl = '';

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
        $this->info("\nWelcome to Pilot! First let's make sure you on the latest version of pilot.\n\n");

        $this->info("Running 'composer update flex360/pilot'...\n\n");

        sleep(5);

        $output = shell_exec('COMPOSER_MEMORY_LIMIT=-1 composer update flex360/pilot');

        $this->info($output . "\n");

        $this->info("Success!\n");

        $this->removeInitialLaravelFiles();

        // link up storage
        $this->call('storage:link');

        $this->updateDatabaseCredentials();

        $this->updateAppUrl();

        $this->setPilotTablePrefix();

        // this will be sure to get the pilot prefix varible from the .env file before migrating
        $this->call('config:clear');

        // migrate the database
        $this->call('migrate');

        //seed the database
        $this->seedDatabase();

        $this->updateAuthConfig();

        $this->createUser();

        $this->addIgnitionVariablesToEnv();

        $this->vendorPublish();

        // this will ensure the backendMiddleWare reruns and creates the new Pages
        $this->call('cache:clear');

        // this will ensure the Standard module routes are registered
        $this->call('route:clear');

        // this will ensure the APP_KEY is set for the application
        $this->call('key:generate');

        $this->info("\n\n\nPilot Takeoff Successful! 🚀 \n\n You can visit your website now: " . $this->appUrl);
    }

    private function removeInitialLaravelFiles()
    {
        $confirm = $this->confirm('Is this a new Laravel install?', true);

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
            $pilotRoutes = "\n\nPilot::routesBefore();\n\n// your backend routes go here\nRoute::middleware(['web', 'pilot.global', 'auth.admin', 'backend'])\n    ->prefix('pilot')\n    ->name('admin.')\n    ->group(function () {\n        /* ---- Dynamo Routes ---- */\n\n});\n\n// your frontend routes go here\nRoute::middleware(['web', 'pilot.global'])\n    ->group(function () {\n\n});\n\nPilot::routesAfter();\n\nRoute::middleware(['web', 'pilot.global'])->group(function () {\n\n// content pages and Department pages\n\nRoute::get('{path?}', ['as' => 'pages',  'uses' => 'SiteController@view'])->where('path', '.+');\n\n});";
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

    private function updateAppUrl()
    {
        // update APP_URL in .env
        $appUrl = $this->ask('App url (ie http://pilot.test)?');
        $this->appUrl = $appUrl;
        $envContent = file_get_contents(base_path('.env'));
        file_put_contents(base_path('.env'), str_replace('APP_URL=http://localhost', 'APP_URL=' . $appUrl, $envContent));
    }

    private function setPilotTablePrefix()
    {
        $confirm = $this->confirm('Do you want a Pilot Table Prefix? (This will prefix the table names for base migration, recommneded)', true);
        if ($confirm) {
            
            $prefix = $this->anticipate('Prefix name? (Recommended: pilot_ )', ['pilot_']);
            //this line create an empty PILOT_TABLE_PREFIX= variable in the env since it doesn't exist with base laravel install
            file_put_contents(base_path('.env'), "\r\n\r\nPILOT_TABLE_PREFIX=", FILE_APPEND);
            $envContent = file_get_contents(base_path('.env'));
            $envContent = str_replace('PILOT_TABLE_PREFIX=', 'PILOT_TABLE_PREFIX=' . $prefix, $envContent);
            file_put_contents(base_path('.env'), $envContent);
        }

        $this->info("Loading...");
        sleep(1);
    }

    private function seedDatabase()
    {
        $confirm = $this->confirm('Success! Do you want seed the database with starter data?', true);
        if ($confirm) {
            $this->call('pilot:seed');
        }
        $this->info("Database seeding sucessfully!");
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
        $confirm = $this->confirm('Would you like to create a user?', true);
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

    private function vendorPublish()
    {
        // publish all the files needed to make Pilot work
        $this->line('Copying support files...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Flex360\Pilot\Providers\PilotServiceProvider'
        ]);
        $this->line('Support files copied!');
    }
}
