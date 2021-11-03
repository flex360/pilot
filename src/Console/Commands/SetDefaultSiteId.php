<?php

namespace Flex360\Pilot\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetDefaultSiteId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:site-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets default site id to 1 anywhere it is null';

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
     * @return int
     */
    public function handle()
    {
        $tables = [
            \Flex360\Pilot\Facades\Post::getTable(),
            \Flex360\Pilot\Facades\Event::getTable(),
            \Flex360\Pilot\Facades\Annoucement::getTable(),
            \Flex360\Pilot\Facades\Resource::getTable(),
            \Flex360\Pilot\Facades\ResourceCategory::getTable(),
            \Flex360\Pilot\Facades\Employee::getTable(),
            \Flex360\Pilot\Facades\Department::getTable(),
            \Flex360\Pilot\Facades\Testimonial::getTable(),
            \Flex360\Pilot\Facades\Faq::getTable(),
            \Flex360\Pilot\Facades\FaqCategory::getTable(),
            \Flex360\Pilot\Facades\Product::getTable(),
            \Flex360\Pilot\Facades\ProductCategory::getTable(),
            \Flex360\Pilot\Facades\Project::getTable(),
            \Flex360\Pilot\Facades\ProjectCategory::getTable(),
            \Flex360\Pilot\Facades\Service::getTable(),
            \Flex360\Pilot\Facades\ServiceCategory::getTable(),
            \Flex360\Pilot\Facades\Tag::getTable(),
            \Flex360\Pilot\Facades\Menu::getTable()
        ];

        foreach ($tables as $table) {
            $this->info('Updating ' . $table . '...');
            $query = DB::table($table)->whereNull('site_id');
            $count = $query->count();
            $query->update(['site_id' => 1]);
            $this->info('Updated ' . $count . ' rows.');
        }
        
        return Command::SUCCESS;
    }
}
