<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBotSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->text('text');
            $table->string('type',190)->unique();
            $table->string('code', 20);
            $table->text('shortcodes');
            $table->timestamps();
        });
        DB::unprepared(file_get_contents(__DIR__.'/05.08.2017-bot_settings-data.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_settings');
    }
}
