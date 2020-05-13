<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceUsdToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->float('price_usd')
                  ->after('price')
                  ->default(0);
            $table->float('total_usd')
                ->after('total')
                ->default(0);
        });
    }


    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('price_usd');
            $table->dropColumn('total_usd');
        });
    }
}
