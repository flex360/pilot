<?php

namespace Flex360\Pilot\Console\Commands;

use Flex360\Pilot\Pilot\Faq;
use Flex360\Pilot\Pilot\Page;
use Flex360\Pilot\Pilot\Post;
use Flex360\Pilot\Pilot\Event;
use Illuminate\Console\Command;
use Flex360\Pilot\Pilot\Product;
use Flex360\Pilot\Pilot\Project;
use Flex360\Pilot\Pilot\Service;
use Flex360\Pilot\Pilot\Employee;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Support\Facades\DB;
use Flex360\Pilot\Pilot\Department;
use Illuminate\Support\Facades\App;
use Flex360\Pilot\Pilot\FaqCategory;
use Flex360\Pilot\Pilot\Testimonial;
use Flex360\Pilot\Pilot\ProductCategory;
use Flex360\Pilot\Pilot\ProjectCategory;
use Flex360\Pilot\Pilot\ServiceCategory;
use Flex360\Pilot\Pilot\ResourceCategory;

class FixMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix model_type on overridden models';

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
        $coreClasses = [
            Department::class,
            Employee::class,
            Event::class,
            Page::class,
            Post::class,
            Resource::class,
            ResourceCategory::class,
            Service::class,
            ServiceCategory::class,
            Product::class,
            ProductCategory::class,
            Faq::class,
            FaqCategory::class,
            Project::class,
            ProjectCategory::class,
            Testimonial::class,
        ];

        foreach ($coreClasses as $coreClass) {
            $instance = App::make($coreClass);
            $currentClass = get_class($instance);
            if ($coreClass != $currentClass) {
                $count = DB::table('media')
                    ->where('model_type', '=', $coreClass)
                    ->count();

                if ($count > 0) {
                    DB::table('media')
                    ->where('model_type', '=', $coreClass)
                    ->update(['model_type' => $currentClass]);

                    $this->line($count . ' media records converted to ' . $currentClass . '.');
                } else {
                    $this->line('No ' . $coreClass . ' media records to convert.');
                }
            } else {
                $this->line($coreClass . ' not extended.');
            }
        }
    }
}
