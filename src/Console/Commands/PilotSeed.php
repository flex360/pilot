<?php

namespace Flex360\Pilot\Console\Commands;

use Exception;
use Carbon\Carbon;
use Flex360\Pilot\Pilot\Faq;
use Flex360\Pilot\Pilot\Post;
use Flex360\Pilot\Pilot\Event;
use Illuminate\Console\Command;
use Flex360\Pilot\Pilot\Product;
use Flex360\Pilot\Pilot\Employee;
use Flex360\Pilot\Pilot\Resource;
use Illuminate\Support\Facades\DB;
use Flex360\Pilot\Pilot\Department;
use Flex360\Pilot\Pilot\Annoucement;
use Flex360\Pilot\Pilot\FaqCategory;
use Flex360\Pilot\Pilot\Testimonial;
use Flex360\Pilot\Pilot\ProductCategory;
use Flex360\Pilot\Pilot\ResourceCategory;

class PilotSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pilot:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed pilot modules with example data';

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
        // create test tag
        DB::table('tags')->insert(
            ['name' => 'Example Tag',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );
        
        // create the Standard Example Event
        DB::table((new Event())->getTable())->insert(
            ['title' => 'Event Example',
             'short_description' => 'This is the short description',
             'start' => Carbon::now()->sub(3, 'days'),
             'end' => Carbon::now()->add(3, 'days'),
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'gallery' => '',
             'image' => '',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
             'published_at' => Carbon::now(),
            ]
        );

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'event_tag')->insert(
            ['event_id' => 1,
             'tag_id' => 1,
            ]
        );

         // create the Standard Post Example
        DB::table((new Post())->getTable())->insert(
            ['title' => 'Post Example',
             'summary' => 'This is the post summary',
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'horizontal_featured_image' => '',
             'vertical_featured_image' => '',
             'gallery' => '',
             'external_link' => '',
             'status' => 10,
             'published_on' => Carbon::now(),
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),             
            ]
        );

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'post_tag')->insert(
            ['post_id' => 1,
             'tag_id' => 1,
            ]
        );

        // create the Standard Example Annoucement
        DB::table((new Annoucement())->getTable())->insert(
            ['headline' => 'Testing Alert Module',
             'short_description' => 'We\'re testing out our new alert module!',
             'button_text' => 'Did it work?',
             'button_link' => '/learn/alert-module-test',
             'status' => 1,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the Standard Example Resource
        DB::table((new Resource())->getTable())->insert(
            ['title' => 'Cute puppies',
             'short_description' => 'This is a test resource that links to cute puppies',
             'link' => 'https://www.google.com/search?ei=LBTMX6zWBpKxtQbhi5OYCw&q=cute+puppies&oq=cute+puppies&gs_lcp=CgZwc3ktYWIQAzIFCAAQsQMyAggAMgIIADICCAAyBQgAEMkDMgIIADICCAAyAggAMgIIADICCAA6BAgAEEc6CAguEJECEJMCOgsILhDHARCjAhCRAjoICAAQsQMQgwE6CAguEMcBEKMCOg4ILhCxAxCDARDHARCjAjoFCAAQkQI6CwguEMcBEK8BEJECOhEILhDHARCvARDJAxCRAhCTAjoFCC4QsQM6CAgAELEDEMkDOgcIABCxAxAKOgQIABAKOgcIABDJAxAKUJwlWKUzYNwzaAFwAXgAgAGCAYgBuAiSAQM3LjSYAQCgAQGqAQdnd3Mtd2l6yAEIwAEB&sclient=psy-ab&ved=0ahUKEwisnLj2-7ftAhWSWM0KHeHFBLMQ4dUDCA0&uact=5',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the Standard Example Resource Category
        DB::table((new ResourceCategory())->getTable())->insert(
            ['name' => 'Example Category',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example resource_resource_category entry
        DB::table(config('pilot.table_prefix') . 'resource_' . config('pilot.table_prefix') . 'resource_category')->insert(
            ['resource_id' => 1,
             'resource_category_id' => 1,
            ]
        );

        // create the Standard Example Employee
        DB::table((new Employee())->getTable())->insert(
            ['photo' => '',
             'first_name' => 'John',
             'last_name' => 'Doe',
             'start_date' => Carbon::now()->sub(7, 'years'),
             'birth_date' => Carbon::now()->sub(25, 'years'),
             'job_title' => 'Account Executive',
             'phone_number' => '555-555-5555',
             'extension' => '317',
             'email' => 'hello@flex360.com',
             'office_location' => 'Little Rock, AR',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the Standard Example Department
        DB::table((new Department())->getTable())->insert(
            ['name' => 'Department Example',
             'intro_text' => 'This is text explaining some stuff about this department in the company',
             'featured_image' => '',
             'slug' => 'department-example',
             'summary' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'position' => 0,
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example employee_department entry
        DB::table(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee')->insert(
            ['employee_id' => 1,
             'department_id' => 1,
             'position' => 0,
            ]
        );

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'department_tag')->insert(
            ['department_id' => 1,
             'tag_id' => 1,
            ]
        );

        // create the example department_resources entry
        DB::table(config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'resource')->insert(
            ['department_id' => 1,
             'resource_id' => 1,
            ]
        );

        // create the Standard Example Testimonial
        DB::table((new Testimonial())->getTable())->insert(
            ['name' => 'Jane Smith',
             'city' => 'Paris',
             'state' => 'Arkansas',
             'country' => '',
             'quote' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the Standard Example Faq
        DB::table((new Faq())->getTable())->insert(
            ['question' => 'What is an FAQ?',
             'answer' => 'An FAQ stands for Frequently Asked Question. It is a question that our customers and friends commonly have for us.',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the Standard Example FaqCategory
        DB::table((new FaqCategory())->getTable())->insert(
            ['name' => 'Payments & Financing',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example faq_faq_category entry
        DB::table(config('pilot.table_prefix') . 'faq_' . config('pilot.table_prefix') . 'faq_category')->insert(
            ['faq_id' => 1,
             'faq_category_id' => 1,
             'position' => 0,
            ]
        );

        // create the Standard Example Product
        DB::table((new Product())->getTable())->insert(
            ['name' => 'Coffee Cup',
             'price' => '100.00',
             'short_description' => 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Perferendis voluptate optio, illo accusamus corporis tempora officiis quos! Eaque deleniti aspernatur recusandae ex qui. Aperiam maiores ad numquam, praesentium tenetur aut, possimus blanditiis molestias, architecto a vel deleniti soluta quae provident voluptatibus repudiandae maxime ducimus incidunt eaque. Voluptas, non qui? Optio!',
             'full_description' => 'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Perferendis voluptate optio, illo accusamus corporis tempora officiis quos! Eaque deleniti aspernatur recusandae ex qui. Aperiam maiores ad numquam, praesentium tenetur aut, possimus blanditiis molestias, architecto a vel deleniti soluta quae provident voluptatibus repudiandae maxime ducimus incidunt eaque. Voluptas, non qui? Optio!',
             'status' => 10,
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the Standard Example ProductCategory
        DB::table((new ProductCategory())->getTable())->insert(
            ['title' => 'Payments & Financing',
             'created_at' => Carbon::now(),
             'updated_at' => Carbon::now(),
            ]
        );

        // create the example product_product_category entry
        DB::table(config('pilot.table_prefix') . 'product_' . config('pilot.table_prefix') . 'product_category')->insert(
            ['product_id' => 1,
             'product_category_id' => 1,
             'position' => 0,
            ]
        );
    }
}
