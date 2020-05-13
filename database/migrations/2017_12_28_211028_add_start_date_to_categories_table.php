<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDateToCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('start_date')
                ->after('visible')
                ->default('00:00');
        });
    }


    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
}
