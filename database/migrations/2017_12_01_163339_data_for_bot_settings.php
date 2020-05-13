<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DataForBotSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => '👨🏻‍💻 Админ',
            'type' => 'Админка',
            'code' => 'button',
        ]);
        DB::table('bot_settings')->insert([
            'text' => '✉️ Сообщения',
            'type' => 'Сообщения',
            'code' => 'button',
        ]);
        DB::table('bot_settings')->insert([
            'text' => '👥 Пользователи',
            'type' => 'Пользователи',
            'code' => 'button',
        ]);
        DB::table('bot_settings')->insert([
            'text' => '✉️ Обратная связь',
            'type' => 'Обратная связь',
            'code' => 'button',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Панель админа',
            'type' => 'Панель админа',
            'code' => 'message',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Aдминистратор: ',
            'type' => 'Aдминистратор: ',
            'code' => 'message',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Я: ',
            'type' => 'Я: ',
            'code' => 'message',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Введите сообщение для админа',
            'type' => 'Введите сообщение для админа',
            'code' => 'message',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Не отвеченные сообщения',
            'type' => 'Не отвеченные сообщения',
            'code' => 'message',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Напишите ответ пользователю',
            'type' => 'Напишите ответ пользователю',
            'code' => 'message',
        ]);
        DB::table('bot_settings')->insert([
            'text' => 'Нет id этого клиента',
            'type' => 'Нет id этого клиента',
            'code' => 'message',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
