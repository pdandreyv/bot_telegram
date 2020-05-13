<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientsToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Клиентов не найдено.',
            'type' => 'Клиентов не найдено',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Клиенты',
            'type' => 'Клиенты',
            'code' => 'button',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Клиентов не найдено')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Клиенты')
            ->delete();
    }
}
