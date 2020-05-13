<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryToAttributesTable extends Migration
{
    public function up()
    {
        DB::table('attributes')->insert([
            'name' => 'Страна',
        ]);
    }


    public function down()
    {
        DB::table('attributes')
            ->where('name', 'Страна')
            ->delete();
    }
}
