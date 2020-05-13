<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalanceMessagesToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => '💰 Баланс',
            'type' => 'Баланс',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Для вас не выбран тип оплаты.',
            'type' => 'Сообщение пользователю без типа оплаты',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => "Ваш баланс: {balance}.",
            'type' => 'Баланс клиента',
            'code' => 'message',
            'shortcodes' => '{balance}',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Ваш дневной лимит {balance}.',
            'type' => 'Баланс исчерпан для оплаты по факту',
            'code' => 'message',
            'shortcodes' => '{balance}',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Ваш баланс {balance} был исчерпан.',
            'type' => 'Баланс исчерпан для предоплаты',
            'code' => 'message',
            'shortcodes' => '{balance}',
        ]);
    }

    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Мой баланс')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Сообщение пользователю без типа оплаты')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Баланс клиента')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Баланс исчерпан для оплаты по факту')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Баланс исчерпан для предоплаты')
            ->delete();
    }
}
