<?php

use Carbon\Carbon;
use Flex360\Pilot\Pilot\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create((new Product())->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('price');
            $table->string('short_description');
            $table->text('full_description');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });

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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists((new Product())->getTable());
    }
}
