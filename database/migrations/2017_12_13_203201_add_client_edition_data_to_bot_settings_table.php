<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientEditionDataToBotSettingsTable extends Migration
{
    public function up()
    {
        DB::table('bot_settings')->insert([
            'text' => 'Далее',
            'type' => 'Далее',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Данные успешно отредактированы.',
            'type' => 'Данные успешно отредактированы',
            'code' => 'message',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Крупный опт',
            'type' => 'Крупный опт',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => 'Мелкий опт',
            'type' => 'Мелкий опт',
            'code' => 'button',
        ]);

        DB::table('bot_settings')->insert([
            'text' => "Имя: {first_name} \r\nФамилия: {last_name} \r\nВы можете изменить данные клиента в поле ввода (через пробел).",
            'type' => 'Редактирование имени клиента',
            'code' => 'message',
            'shortcodes' => '{first_name} {last_name}',
        ]);

        DB::table('bot_settings')->insert([
            'text' => "Город: {city} \r\nВы можете изменить город клиента в поле ввода.",
            'type' => 'Редактирование города клиента',
            'code' => 'message',
            'shortcodes' => '{city}',
        ]);
    }


    public function down()
    {
        DB::table('bot_settings')
            ->where('type', 'Далее')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Данные успешно отредактированы')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Крупный опт')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Мелкий опт')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Редактирование имени клиента')
            ->delete();

        DB::table('bot_settings')
            ->where('type', 'Редактирование города клиента')
            ->delete();
    }
}
