<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attribute_id');
            $table->string('value', 100)->unique();
            $table->timestamps();
        });
        DB::table('attribute_values')->insert([
            ['attribute_id' => '1','value'=>'16 Gb'],
            ['attribute_id' => '1','value'=>'32 Gb'],
            ['attribute_id' => '1','value'=>'64 Gb'],
            ['attribute_id' => '1','value'=>'128 Gb'],
            ['attribute_id' => '2','value'=>'Black'],
            ['attribute_id' => '2','value'=>'White'],
            ['attribute_id' => '2','value'=>'Gold'],
            ['attribute_id' => '2','value'=>'Grey'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attribute_values');
    }
}
