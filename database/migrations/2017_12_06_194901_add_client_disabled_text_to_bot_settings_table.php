<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientDisabledTextToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Пользователь отключен.',
            'type' => 'Пользователь отключен',
            'code' => 'message',
        ]);
    }

    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Пользователь отключен')
            ->delete();
    }
}
