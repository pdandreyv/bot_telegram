<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOneHands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_hands', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->integer('product_id');
            $table->integer('counts');
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('one_hand_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
