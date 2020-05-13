<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptsToPercentsTable extends Migration
{
    public function up()
    {
        Schema::table('percents', function (Blueprint $table) {
            $table->dropColumn('value');

            $table->float('percentLarge')
                ->after('id')
                ->default(0.1);

            $table->float('percentMiddle')
                ->after('percentLarge')
                ->default(0.1);

            $table->float('percentSmall')
                ->after('percentMiddle')
                ->default(0.1);
        });


    }


    public function down()
    {
        Schema::table('percents', function (Blueprint $table) {
            $table->float('value')
                ->default(0.25);

            $table->dropColumn(['percentLarge', 'percentMiddle', 'percentSmall']);
        });
    }
}
