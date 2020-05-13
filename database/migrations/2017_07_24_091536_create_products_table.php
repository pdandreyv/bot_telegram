<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->integer('position');
            $table->string('name',50)->unique();
            $table->string('country',50);
            $table->integer('quantity');
            $table->smallInteger('one_hand');
            $table->float('price');
            $table->float('price_opt');
            $table->integer('addition_count');
            $table->float('addition_price');
            $table->integer('buy_count');
            $table->timestamps();
        });
        DB::unprepared(file_get_contents(__DIR__.'/05.08.2017-products-data.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
