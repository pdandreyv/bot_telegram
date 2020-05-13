<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeywordMessagesToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Введите ключ:',
            'type' => 'Введите ключ',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Ключ верный. Дождитесь авторизации.',
            'type' => 'Ключ верный',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Неверный ключ. Попробуйте еще раз:',
            'type' => 'Ключ неверный',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'На сегодня заказов нет',
            'type' => 'На сегодня заказов нет',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Заказы',
            'type' => 'Заказы',
            'code' => 'button',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Введите ключ')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Ключ верный')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Ключ неверный')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'На сегодня заказов нет')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Заказы')
            ->delete();

    }
}
