
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceMiddleToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('price_middle')
                ->after('price_old')
                ->nullable();
            $table->float('price_middle_old')
                ->after('price_middle')
                ->nullable();
        });
    }


    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_middle', 'price_middle_old']);
        });
    }
}
