<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentTypesAndSumColumnsToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('payment_type_id')
                  ->after('uid')
                  ->nullable();

            $table->float('max_amount', 20, 2)
                  ->after('city')
                  ->default(0);
        });
    }


    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('payment_type_id');
            $table->dropColumn('max_amount');
        });
    }
}
