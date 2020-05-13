<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalColumnToAttributeValuesTable extends Migration
{
    public function up()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->string('additional_data')
                  ->after('value')
                  ->nullable();
        });
    }


    public function down()
    {
        Schema::table('attribute_values', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
    }
}
