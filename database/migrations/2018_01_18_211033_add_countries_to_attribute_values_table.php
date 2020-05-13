<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountriesToAttributeValuesTable extends Migration
{
    public function up()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            DB::table('attribute_values')->insert([
                ['attribute_id' => '3','value'=>'КНР'],
                ['attribute_id' => '3','value'=>'США'],
            ]);
        });
    }

    public function down()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            //
        });
    }
}
