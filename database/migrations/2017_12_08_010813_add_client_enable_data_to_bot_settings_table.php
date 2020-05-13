<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientEnableDataToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Пользователь включен.',
            'type' => 'Пользователь включен',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Включить',
            'type' => 'Включить',
            'code' => 'button',
        ]);
    }

    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Пользователь включен')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Включить')
            ->delete();
    }
}
