<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewPriceToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('price_without_extra_charge')
                ->after('price')
                ->nullable();
            $table->integer('total_without_extra_charge')
                ->after('total')
                ->nullable();
        });
    }


    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['price_without_extra_charge', 'total_without_extra_charge']);
        });
    }
}
