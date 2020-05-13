<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypesToPaymentTypesTable extends Migration
{
    public function up()
    {
        Schema::table('payment_types', function (Blueprint $table) {
            DB::table('payment_types')->insert([
                'name' => 'По факту',
                'code' => 'in_fact',
            ]);

            DB::table('payment_types')->insert([
                'name' => 'По предоплате',
                'code' => 'by_prepayment',
            ]);
        });
    }


    public function down()
    {
        Schema::table('payment_types', function (Blueprint $table) {
            DB::table('payment_types')
                ->where('code', 'in_fact')
                ->delete();

            DB::table('payment_types')
                ->where('code', 'by_prepayment')
                ->delete();
        });
    }
}
