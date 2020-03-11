<?php

namespace Flex360\Pilot\Console\Commands;

use Illuminate\Console\Command;
use Flex360\Pilot\Pilot\Wufoo\WufooForm;

class SyncForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync form data';

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
        $results = WufooForm::syncAll();

        $this->comment('Sync completed with the following results:');

        foreach ($results as $hash => $count) {
            $form = WufooForm::getFormByHash($hash);
            $this->info($form['name'] . ' => ' . $count . ' records');
        }
    }
}
