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
        // create test tags
        DB::table('tags')->insert(
            ['name' => 'Example Tag',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
            ]
        );

        DB::table('tags')->insert(
            ['name' => 'Should Not Display',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
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
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
             'published_at' => '2021-04-16 08:55:00',
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
            ['title' => 'Post Example (No Images)',
             'summary' => 'This is a meta description. Sit amet mattis vulputate enim nulla aliquet porttitor lacus luctus accumsan tortor sit amet mattis vulputate enim nulla aliquet porttitor lacus luctus accumsan tortor.',
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'fi_background_color' => '#ffffff',
             'horizontal_featured_image' => '',
             'vertical_featured_image' => '',
             'gallery' => '',
             'external_link' => '',
             'status' => 30,
             'sticky' => 0,
             'slug' => 'post-example',
             'published_on' => '2021-04-16 08:55:00',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',             
            ]
        );
        DB::table((new Post())->getTable())->insert(
            ['title' => 'Post Example (No Meta Description)',
             'summary' => 'This is the post summary',
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'fi_background_color' => '#ffffff',
             'horizontal_featured_image' => '',
             'vertical_featured_image' => '',
             'gallery' => '',
             'external_link' => '',
             'status' => 30,
             'sticky' => 0,
             'slug' => 'post-example-no-meta',
             'published_on' => '2021-04-16 08:55:00',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',             
            ]
        );
        DB::table((new Post())->getTable())->insert(
            ['title' => 'Post Example w/ Featured Image',
             'summary' => 'This is a sample post with featured image, body, gallery of multi-shaped images with photo title, credit, description on the first image. This is what a news post will look like with every element utilized.',
             'body' => '<h2>Example text</h2>

             <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Consectetur libero id faucibus nisl tincidunt eget nullam. Nunc sed blandit libero volutpat sed cras ornare. Lorem dolor sed viverra ipsum nunc aliquet. Eleifend quam adipiscing vitae proin sagittis. Massa vitae tortor condimentum lacinia quis vel eros. Odio ut enim blandit volutpat maecenas volutpat blandit aliquam etiam. Praesent elementum facilisis leo vel fringilla. Tortor pretium viverra suspendisse potenti nullam. Convallis convallis tellus id interdum velit laoreet id donec. Non sodales neque sodales ut etiam sit amet nisl. Nunc id cursus metus aliquam eleifend mi in nulla. Ipsum dolor sit amet consectetur adipiscing. Ut morbi tincidunt augue interdum. Ut eu sem integer vitae justo eget magna fermentum iaculis. Nulla at volutpat diam ut venenatis tellus in metus vulputate. Felis donec et odio pellentesque diam volutpat. Tempus urna et pharetra pharetra massa</p>
             
             <h3>Aliquam malesuada bibendum arcu vitae.&nbsp;</h3>
             
             <p>Blandit libero volutpat sed cras ornare arcu. Mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Aliquam purus sit amet luctus venenatis lectus magna fringilla. Volutpat odio facilisis mauris sit amet massa vitae tortor. Sed risus pretium quam vulputate dignissim suspendisse in est. Lacus laoreet non curabitur gravida arcu.&nbsp;</p>
             
             <ul>
                 <li>Pretium aenean pharetra magna ac placerat vestibulum.&nbsp;</li>
                 <li>Est velit egestas dui id ornare arcu.&nbsp;</li>
                 <li>Vulputate mi sit amet mauris commodo quis.&nbsp;</li>
                 <li>Ut venenatis tellus in metus vulputate eu scelerisque felis imperdiet.&nbsp;</li>
             </ul>
             
             <p>Habitasse platea dictumst quisque sagittis purus sit amet volutpat. A erat nam at lectus urna duis. Non arcu risus quis varius quam quisque. Arcu risus quis varius quam quisque id diam. Rhoncus mattis rhoncus urna neque.</p>
             
             <p>Sit amet porttitor eget dolor morbi. At tempor commodo ullamcorper a lacus vestibulum sed arcu non. Consectetur adipiscing elit duis tristique. Morbi tempus iaculis urna id volutpat. Egestas sed tempus urna et pharetra pharetra massa. Aliquam sem fringilla ut morbi tincidunt augue interdum velit. Aliquet porttitor lacus luctus accumsan tortor posuere ac ut consequat. Vestibulum mattis ullamcorper velit sed ullamcorper morbi tincidunt ornare. Eget duis at tellus at urna condimentum mattis. Amet commodo nulla facilisi nullam vehicula ipsum a arcu. Urna nec tincidunt praesent semper feugiat nibh sed pulvinar proin. Neque laoreet suspendisse interdum consectetur libero. Cras sed felis eget velit aliquet sagittis. Tristique sollicitudin nibh sit amet commodo nulla facilisi nullam vehicula. Egestas congue quisque egestas diam. Tellus mauris a diam maecenas sed.</p>
             
             <p>Vitae ultricies leo integer malesuada nunc vel risus commodo viverra. Porttitor eget dolor morbi non arcu risus. Aliquam ultrices sagittis orci a. Viverra maecenas accumsan lacus vel facilisis volutpat. Vitae sapien pellentesque habitant morbi tristique senectus et. Interdum posuere lorem ipsum dolor sit amet consectetur adipiscing. Aenean sed adipiscing diam donec. Accumsan tortor posuere ac ut. Nunc sed blandit libero volutpat sed cras. Turpis in eu mi bibendum neque egestas congue quisque egestas. Quam nulla porttitor massa id neque aliquam vestibulum. Diam volutpat commodo sed egestas egestas fringilla phasellus. Risus viverra adipiscing at in tellus integer feugiat scelerisque varius. Ac feugiat sed lectus vestibulum. Purus gravida quis blandit turpis cursus.</p>
             
             <p>Est ullamcorper eget nulla facilisi etiam dignissim. Hac habitasse platea dictumst vestibulum. Fringilla est ullamcorper eget nulla facilisi etiam. Urna molestie at elementum eu facilisis sed. Imperdiet massa tincidunt nunc pulvinar sapien et ligula. Lacinia quis vel eros donec ac odio. Nisl nunc mi ipsum faucibus. Mattis nunc sed blandit libero volutpat sed. Sit amet consectetur adipiscing elit duis tristique sollicitudin nibh sit. Tempor id eu nisl nunc mi ipsum faucibus vitae aliquet. Et tortor at risus viverra adipiscing at. Purus non enim praesent elementum facilisis leo vel.</p>
             ',
             'fi_background_color' => '#ffffff',
             'horizontal_featured_image' => '',
             'vertical_featured_image' => '',
             'gallery' => '',
             'external_link' => '',
             'status' => 30,
             'sticky' => 1,
             'slug' => 'post-example-images',
             'published_on' => '2021-04-16 08:55:00',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',             
            ]
        );

        DB::table((new Post())->getTable())->insert(
            ['title' => 'Post Example (Draft)',
             'summary' => 'This post is saved as a Draft and should not display on the front end. The tag "Should Not Display" also should not be displaying on the front-end.',
             'body' => 'Example text: Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
             'fi_background_color' => '#ffffff',
             'horizontal_featured_image' => '',
             'vertical_featured_image' => '',
             'gallery' => '',
             'external_link' => '',
             'status' => 10,
             'sticky' => 0,
             'slug' => 'post-example-draft',
             'published_on' => '2021-04-16 08:55:00',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',             
            ]
        );

        $post = Post::find(3);
        //featured image
        $post->addMedia(public_path('pilot-assets/img/FLEX360_learn.jpg'))->preservingOriginal()->toMediaCollection('horizontal_featured_image');

        //gallery images
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/1.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/2.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/3.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/4.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/5.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/6.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/7.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/8.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/9.jpeg'))->preservingOriginal()->toMediaCollection('gallery');
        $post->addMedia(public_path('pilot-assets/img/galleryExampleImages/10.jpeg'))->preservingOriginal()->toMediaCollection('gallery');

        // create the example department_tag entry
        DB::table(config('pilot.table_prefix') . 'post_tag')->insert(
            ['post_id' => 1,
             'tag_id' => 1,
            ]
        );
        DB::table(config('pilot.table_prefix') . 'post_tag')->insert(
            ['post_id' => 2,
             'tag_id' => 1,
            ]
        );
        DB::table(config('pilot.table_prefix') . 'post_tag')->insert(
            ['post_id' => 3,
             'tag_id' => 1,
            ]
        );
        DB::table(config('pilot.table_prefix') . 'post_tag')->insert(
            ['post_id' => 4,
             'tag_id' => 2,
            ]
        );

        // create the Standard Example Annoucement
        DB::table((new Annoucement())->getTable())->insert(
            ['headline' => 'Testing Alert Module',
             'short_description' => 'We\'re testing out our new alert module!',
             'button_text' => 'Did it work?',
             'button_link' => '/learn/alert-module-test',
             'status' => 1,
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
            ]
        );

        // create the Standard Example Resource
        DB::table((new Resource())->getTable())->insert(
            ['title' => 'Cute puppies',
             'short_description' => 'This is a test resource that links to cute puppies',
             'link' => 'https://www.google.com/search?ei=LBTMX6zWBpKxtQbhi5OYCw&q=cute+puppies&oq=cute+puppies&gs_lcp=CgZwc3ktYWIQAzIFCAAQsQMyAggAMgIIADICCAAyBQgAEMkDMgIIADICCAAyAggAMgIIADICCAA6BAgAEEc6CAguEJECEJMCOgsILhDHARCjAhCRAjoICAAQsQMQgwE6CAguEMcBEKMCOg4ILhCxAxCDARDHARCjAjoFCAAQkQI6CwguEMcBEK8BEJECOhEILhDHARCvARDJAxCRAhCTAjoFCC4QsQM6CAgAELEDEMkDOgcIABCxAxAKOgQIABAKOgcIABDJAxAKUJwlWKUzYNwzaAFwAXgAgAGCAYgBuAiSAQM3LjSYAQCgAQGqAQdnd3Mtd2l6yAEIwAEB&sclient=psy-ab&ved=0ahUKEwisnLj2-7ftAhWSWM0KHeHFBLMQ4dUDCA0&uact=5',
             'status' => 10,
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
            ]
        );

        // create the Standard Example Resource Category
        DB::table((new ResourceCategory())->getTable())->insert(
            ['name' => 'Example Category',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
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
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
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
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
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
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
            ]
        );

        // create the Standard Example Faq
        DB::table((new Faq())->getTable())->insert(
            ['question' => 'What is an FAQ?',
             'answer' => 'An FAQ stands for Frequently Asked Question. It is a question that our customers and friends commonly have for us.',
             'status' => 10,
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
            ]
        );

        // create the Standard Example FaqCategory
        DB::table((new FaqCategory())->getTable())->insert(
            ['name' => 'Payments & Financing',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
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
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
            ]
        );

        // create the Standard Example ProductCategory
        DB::table((new ProductCategory())->getTable())->insert(
            ['title' => 'Payments & Financing',
             'created_at' => '2021-04-16 08:55:00',
             'updated_at' => '2021-04-16 08:55:00',
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
