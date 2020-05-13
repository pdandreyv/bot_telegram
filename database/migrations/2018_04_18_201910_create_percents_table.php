<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePercentsTable extends Migration
{
    public function up()
    {
        Schema::create('percents', function (Blueprint $table) {
            $table->increments('id');
            $table->float('value')
                ->default(0.25);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::table('percents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('percents', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::dropIfExists('percents');
    }
}
