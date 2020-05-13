<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProduct4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('rate')->after('addition_price');
            $table->integer('weight')->after('rate');
            $table->float('tariff')->after('weight');
        });
        DB::table('bot_settings')->insert([
            'text' => '56.7',
            'type' => 'rate',
            'code' => 'other',
        ]);
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
